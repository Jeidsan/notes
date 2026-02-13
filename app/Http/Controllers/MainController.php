<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Http\Request;

use function Laravel\Prompts\note;

class MainController extends Controller
{
    public function index()
    {
        $id = session('user.id');
        $notes = User::find($id)->notes()->whereNull('deleted_at')->get()->toArray();

        return view('home', ['notes' => $notes]);
    }

    public function newNote()
    {
        return view('new_note');
    }

    public function newNoteSubmit(Request $request)
    {
        $request->validate(
            [
                'text_title' => 'required|min:6|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            [
                'text_title' => 'Deve ser informado um título para a nota.',
                'text_title.min' => 'O título deve conter pelo menos :min caracteres.',
                'text_title.max' => 'O título deve conter no máximo :max caracteres',
                'text_note' => 'O texto da nota deve ser informado.' ,
                'text_note.min' => 'A nota deve conter pelo menos :min caractere(s).',
                'text_nota.max' => 'A nota deve conter no máximo :max caracteres.'
            ]
        );

        $id = session('user.id');

        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;

        $note->save();

        return redirect()->route('home');
    }

    public function editNote($id)
    {
        $id = Operations::decryptId($id);

        if ( $id == null )
            return redirect()->route('home');

        $note = Note::find($id);

        return view('edit_note', [ 'note' => $note ]);
    }

    public function editNoteSubmit(Request $request)
    {
        $request->validate(
            [
                'text_title' => 'required|min:6|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            [
                'text_title' => 'Deve ser informado um título para a nota.',
                'text_title.min' => 'O título deve conter pelo menos :min caracteres.',
                'text_title.max' => 'O título deve conter no máximo :max caracteres',
                'text_note' => 'O texto da nota deve ser informado.' ,
                'text_note.min' => 'A nota deve conter pelo menos :min caractere(s).',
                'text_nota.max' => 'A nota deve conter no máximo :max caracteres.'
            ]
        );

        if ($request->note_id == null)
            return redirect()->route('home');

        $id = Operations::decryptId($request->note_id);

        if ( $id == null )
            return redirect()->route('home');

        $note = Note::find($id);
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();

        return redirect()->route('home');
    }

    public function deleteNote($id)
    {
        $id = Operations::decryptId($id);

        if ( $id == null )
            return redirect()->route('home');

        $note = Note::find($id);

        return view('delete_note', ['note' => $note]);
    }

    public function deleteNoteConfirm($id)
    {
        $id = Operations::decryptId($id);

        if ( $id == null )
            return redirect()->route('home');

        $note = Note::find($id);

        // Hard delete
        // $note->delete();

        // Soft delete
        // $note->deleted_at = date('Y-m-d H:i:s');
        // $note->save();

        // Outra forma de soft delete é adicionar o `use SoftDeletes` na classe Note e fazer o delete normal.
        $note->delete();

        return redirect()->route('home');
    }
}
