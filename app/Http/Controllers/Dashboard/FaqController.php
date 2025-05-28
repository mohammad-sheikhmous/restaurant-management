<?php

namespace App\Http\Controllers\Dashboard;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all()->each(function ($faq) {
            $faq->mergeCasts(['question' => Translated::class, 'answer' => Translated::class]);
        });

        return dataJson('faqs', $faqs, 'all faqs');
    }

    public function show($id)
    {
        $faq = Faq::find($id);
        if (!$faq)
            return messageJson("faq with id: $id not found");

        return dataJson('faq', $faq, "faq with id: $id returned");
    }

    public function store(FaqRequest $request)
    {
        Faq::create($request->only('question', 'answer'));

        return messageJson('new faq created', true, 201);
    }

    public function update(FaqRequest $request, $id)
    {
        $faq = Faq::find($id);
        if (!$faq)
            return messageJson("faq with id: $id not found");

        $faq->update($request->only('question', 'answer'));

        return messageJson("faq with id: $id updated");
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);
        if (!$faq)
            return messageJson("faq with id: $id not found");

        $faq->delete();

        return messageJson("the faq with id: $id deleted");
    }
}
