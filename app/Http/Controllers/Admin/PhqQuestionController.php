<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhqQuestion;
use Illuminate\Http\Request;

class PhqQuestionController extends Controller
{
    public function index()
    {
        $questions = PhqQuestion::orderBy('id', 'asc')->get();
        return view('admin.phq.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'option_0' => 'required|string',
            'option_1' => 'required|string',
            'option_2' => 'required|string',
            'option_3' => 'required|string',
        ]);

        $options = [
            ['score' => 0, 'text' => $request->option_0],
            ['score' => 1, 'text' => $request->option_1],
            ['score' => 2, 'text' => $request->option_2],
            ['score' => 3, 'text' => $request->option_3],
        ];

        PhqQuestion::create([
            'question_text' => $request->question_text,
            'options' => $options,
        ]);

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $question = PhqQuestion::findOrFail($id);

        $question->update([
            'question_text' => $request->question_text,
            'options' => [
                ['score' => 0, 'text' => $request->option_0],
                ['score' => 1, 'text' => $request->option_1],
                ['score' => 2, 'text' => $request->option_2],
                ['score' => 3, 'text' => $request->option_3],
            ]
        ]);

        return redirect()->back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PhqQuestion::destroy($id);
        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }
}
