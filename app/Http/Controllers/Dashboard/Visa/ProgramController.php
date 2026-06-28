<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\ProgramDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\ProgramRequest;
use App\Models\Visa\Program;

class ProgramController extends Controller
{
    public function index(ProgramDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.programs.index');
    }

    public function create()
    {
        return view('dashboard.visa.programs.create');
    }

    public function store(ProgramRequest $request)
    {
        $program = Program::create($request->getSanitized());
        session()->flash('message', 'Program Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.programs.edit', $program);
    }

    public function edit(Program $program)
    {
        return view('dashboard.visa.programs.edit', compact('program'));
    }

    public function update(ProgramRequest $request, Program $program)
    {
        $program->update($request->getSanitized());
        session()->flash('message', 'Program Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return response()->json([
            'message' => 'Program Deleted Successfully!',
        ]);
    }
}
