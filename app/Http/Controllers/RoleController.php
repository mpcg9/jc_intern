<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller {
    /**
     * RoleController constructor.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('role.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'label' => 'required|alpha_num',
            'can_plan_rehearsal' => 'required|boolean',
            'can_plan_gig' => 'required|boolean',
            'can_send_mail' => 'required|boolean',
            'can_configure_system' => 'required|boolean',
            'only_own_voice' => 'required|boolean',
        ]);

        $role = Role::create($request->all());

        $request->session()->flash('message_success', trans('role.success', ['label' => $role->label]));

        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //TODO: Implement properly
    public function show($id) {
        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //TODO: Implement properly
    public function edit($id) {
        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $role = Role::find($id);

        if (null === $role) {
            return redirect()->route('roles.index', [
                'roles' => Role::all()
            ])->withErrors([trans('role.not_found')]);
        }

        $this->validate($request, [
            'label' => 'required|alpha_num',
            'can_plan_rehearsal' => 'required|boolean',
            'can_plan_gig' => 'required|boolean',
            'can_send_mail' => 'required|boolean',
            'can_configure_system' => 'required|boolean',
            'only_own_voice' => 'required|boolean',
        ]);

        $role->update($request->all());
        $role->save();

        $request->session()->flash('message_success', trans('role.success', ['label' => $role->label]));

        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id) {
        $role = Role::find($id);

        if (null === $role) {
            return redirect()->route('roles.index', [
                'roles' => Role::all()
            ])->withErrors([trans('role.not_found')]);
        }

        $role->delete();

        \Session::flash('message_success', trans('role.delete_success'));

        return redirect()->route('roles.index', [
            'roles' => Role::all()
        ]);
    }
}
