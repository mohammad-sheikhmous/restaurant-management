<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Resource\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()
            ->evaluation()
            ->with(['category:id,name'])
            ->paginate(\request()->limit ?? 10);

        $max_orders = $products->max('orders_count');
        $max_revenue = $products->max('revenue');
        $max_unique_users = $products->max('unique_users');

        $products = $products->through(function ($product) use ($max_orders, $max_revenue, $max_unique_users) {
            return $this->getStatsFactors($product, $max_orders, $max_revenue, $max_unique_users);
        });

        $page = numberToOrdinalWord(request()->page ?? 1);

        return dataJson(
            'products',
            (ProductResource::collection($products))->response()->getData(true),
            "All Products for {$page} page."
        );
    }

    public function show($id)
    {
        if (request()->for == 'showing') {
            $product = Product::where('id', $id)
                ->select('products.*', 't2.max_orders', 't2.max_unique_users', 't2.max_revenue')
                ->with(['tags', 'category:id,name', 'options.attribute'])
                ->evaluation()
                ->globalStats()
                ->first();
            if (!$product)
                return messageJson('Product not found.!', false, 404);

            $product = $this->getStatsFactors($product);
        } else {
            $product = Product::where('id', $id)
                ->with(['tags', 'category:id,name', 'options.attribute'])
                ->first();
            if (!$product)
                return messageJson('Product not found.!', false, 404);
        }

        return dataJson(
            'product',
            ProductResource::make($product),
            "Product with id: $id returned."
        );
    }

    public function getStatsFactors($product, $max_orders = 0, $max_revenue = 0, $max_unique_users = 0)
    {
        $max_orders = $product->max_orders ?? $max_orders;
        $max_revenue = $product->max_revenue ?? $max_revenue;
        $max_unique_users = $product->max_unique_users ?? $max_unique_users;

        // Relative Factors
        $product->orders_factor = $max_orders > 0 ? ceil($product->orders_count / $max_orders * 100) : 0;
        $product->revenue_factor = $max_revenue > 0 ? ceil($product->revenue / $max_revenue * 100) : 0;
        $product->unique_users_factor = $max_unique_users > 0 ? ceil($product->unique_users / $max_unique_users * 100) : 0;

        // Calculate repeat rate
        $product_users = DB::table('order_items')
            ->join('orders', 'order_id', 'orders.id')
            ->where('product_id', $product->id)
            ->whereIn('orders.status', ['pending', 'accepted', 'rejected', 'preparing', 'prepared',
                'delivering', 'delivered', 'picked_up'])
            ->select('user_data->name as user_name', DB::raw('count(distinct orders.id) as orders_count'))
            ->groupBy('user_data->name')
            ->get();

        $total_users = $product_users->count();
        $repeat_users = $product_users->where('orders_count', '>', 1)->count();
        $product->repeat_rate = $total_users > 0 ? ceil($repeat_users / $total_users * 100) : 0;

        $product->popularity_score =
            ceil((
                ($product->orders_factor * 0.4) +
                ($product->revenue_factor * 0.3) +
                ($product->unique_users_factor * 0.15) +
                ($product->repeat_rate * 0.15)
            ));

        return $product;
    }

    public function store(ProductRequest $request)
    {
        try {
            $data = $request->except('image');
            $data['image'] = storeImage($request->name['en'], $request->image, 'products');

            $data['price'] = $data['is_simple'] == 0 ?
                $data['basic_opts_prices'][array_search($data['default_basic_opt'], $data['basic_options_ids'])] :
                $data['price'];

            DB::beginTransaction();
            $product = Product::create($data);

            $product->tags()->attach($data['tags_ids']);

            // Attach basic option
            if ($data['is_simple'] == 0) {
                $product->options()->attach(
                    collect($data['basic_options_ids'])->mapWithKeys(function ($value, $key) use ($data) {
                        return [
                            $value => [ // basic_option_id (value)
                                'extra_price' => $data['basic_opts_prices'][$key],
                                'is_default' => $data['default_basic_opt'] == $value,
                            ]
                        ];
                    })->toArray()
                );
            }
            // Attach additional options
            foreach ($data['additional_options_ids'] as $key1 => $value1) {
                $product->options()->attach(
                    collect($value1)->mapWithKeys(function ($value2, $key2) use ($data, $key1) {
                        return [
                            $value2 => [ // basic_option_id (value)
                                'extra_price' => $data['extra_prices'][$key1][$key2],
                                'is_default' => in_array($value2, $data['default_add_opts']),
                            ]
                        ];
                    })->toArray()
                );
            }
            DB::commit();

            return messageJson('New product created successfully.', true, 201);

        } catch (\Exception $exception) {
            DB::rollBack();

            return exceptionJson();
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::where('id', $id)->with(['tags', 'options.attribute'])->first();
            if (!$product)
                return messageJson('Product not found.!', false, 404);

            $data = $request->except('image');
            if ($request->hasFile('image'))
                $data['image'] = updateImage($request->name['en'], $product->image, $request->image, 'products');

            $data['price'] = $data['is_simple'] == 0 ?
                $data['basic_opts_prices'][array_search($data['default_basic_opt'], $data['basic_options_ids'])] :
                $data['price'];

            DB::beginTransaction();
            $product->update($data);

            // sync new tags selected after update
            $product->tags()->sync($data['tags_ids']);

            $old_basic_opts_ids = $product->options
                ->filter(fn($opt) => $opt->attribute->type == 'basic')
                ->pluck('id')
                ->toArray();

            if ($data['is_simple'])
                unset($data['basic_options_ids']);

            // Detach old basic option after update
            $product->options()->detach(array_diff($old_basic_opts_ids, $data['basic_options_ids'] ?? []));

            // Sync without detaching new basic option after update
            if ($data['is_simple'] == 0) {
                $product->options()->syncWithoutDetaching(
                    collect($data['basic_options_ids'])->mapWithKeys(function ($value, $key) use ($data) {
                        return [
                            $value => [ // basic_option_id (value)
                                'extra_price' => $data['basic_opts_prices'][$key],
                                'is_default' => $data['default_basic_opt'] == $value,
                            ]
                        ];
                    })->toArray()
                );
            }

            $old_add_opts_ids = $product->options
                ->filter(fn($opt) => $opt->attribute->type == 'additional')
                ->pluck('id')
                ->toArray();

            // Detach old additional options after update
            $product->options()->detach(array_diff($old_add_opts_ids, Arr::flatten($data['additional_options_ids'])));

            // Sync without detaching additional options
            foreach ($data['additional_options_ids'] as $key1 => $value1) {
                $product->options()->syncWithoutDetaching(
                    collect($value1)->mapWithKeys(function ($value2, $key2) use ($data, $key1) {
                        return [
                            $value2 => [ // basic_option_id (value)
                                'extra_price' => $data['extra_prices'][$key1][$key2],
                                'is_default' => in_array($value2, $data['default_add_opts']),
                            ]
                        ];
                    })->toArray()
                );
            }
            DB::commit();

            return messageJson('Product updated successfully.');

        } catch (\Exception $exception) {
            DB::rollBack();

            return $exception;
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product)
            return messageJson('Product not found.!', false, 404);

        $product->delete();

        return messageJson('Product deleted successfully.');
    }

    public function change($id)
    {
        request()->validate([
            'field' => 'required|in:is_recommend,status'
        ]);

        $product = Product::find($id);
        if (!$product)
            return messageJson('Product not found.!', false, 404);

        if (request()->field == 'status') {
            $product->update(['status' => !$product->status]);
            $message = 'Product status changed';
        } else {
            $product->update(['is_recommended' => !$product->is_recommended]);
            $message = 'Product recommended.';
        }

        return messageJson($message);
    }
}
