<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'available_variables' => 'nullable|array',
        ]);

        EmailTemplate::create($validated);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Şablon oluşturuldu!');
    }

    public function edit(EmailTemplate $template)
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'available_variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Şablon güncellendi!');
    }

    public function destroy(EmailTemplate $template)
    {
        $template->delete();
        return redirect()
            ->route('templates.index')
            ->with('success', 'Şablon silindi!');
    }

    public function show(EmailTemplate $template)
    {
        return response()->json($template);
    }
}
