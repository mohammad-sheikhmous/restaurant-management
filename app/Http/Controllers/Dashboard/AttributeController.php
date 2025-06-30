<?php

namespace App\Http\Controllers\Dashboard;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('options')->get()->each(function ($attribute) {
            return $attribute->mergeCasts(['name' => Translated::class])->options->each(function ($option) {
                return $option->mergeCasts(['name' => Translated::class]);
            });
        });

        return dataJson('attributes', $attributes, 'all attributes');
    }

    public function show($id)
    {
        $attribute = Attribute::with('options')->find($id);
        if (!$attribute)
            return messageJson('attribute not found', false, 404);

        return dataJson('attribute', $attribute, "attribute with id: $id");
    }

    public function store(AttributeRequest $request)
    {
        try {
            DB::beginTransaction();
            $attribute = Attribute::create([
                'name' => $request->name,
                'type' => $request->type,
            ]);
            foreach ($request->options as $option)
                $attribute->options()->create(['name' => $option]);

            DB::commit();
            return messageJson('new attribute created', true, 201);

        } catch (\Exception $exception) {
            DB::rollBack();

            return exceptionJson();
        }
    }

    public function update(AttributeRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $attribute = Attribute::with('options')->find($id);
            if (!$attribute)
                return messageJson('attribute not found', false, 404);

            $attribute->update($request->only('name', 'type'));
            
            // get deselected options to delete them
            $deselected_options = $attribute->options->filter(function ($option) use ($request) {
                return !in_array($option->getTranslations('name'), $request->options);
            });
            foreach ($deselected_options as $option)
                $option->delete();

            // get just new options to create new instance for them
            $new_options = array_filter($request->options, function ($option1) use ($attribute) {
                return !$attribute->options->first(function ($option2) use ($option1) {
                    return $option2->getTranslations('name') == $option1;
                });
            });
            foreach ($new_options as $option)
                $attribute->options()->create(['name' => $option]);

            DB::commit();
            return messageJson("attribute with id: $id updated");

        } catch (\Exception $exception) {
            DB::rollBack();
            return exceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $attribute = Attribute::whereId($id)->with('productsOptions.product')->first();
            if (!$attribute)
                return messageJson('attribute not found', false, 404);

            if ($attribute->type == 'basic')
                $attribute->productsOptions->each(function ($option) {
                    $option->product()->update(['is_simple' => 1]);
                });
            $attribute->delete();

            DB::commit();
            return messageJson("attribute with id: $id deleted");

        } catch (\Exception $exception) {
            DB::rollBack();

            return exceptionJson();
        }
    }
}
