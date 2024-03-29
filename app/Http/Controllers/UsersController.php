<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return User::select('id', 'username', 'email', 'role_id')->with('role')->get();
    }

    public function orders($id)
    {
        return User::find($id)->orders;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'role_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        $user = new User;
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');

        $role = Role::find($request->input('role_id'));
        $role->users()->save($user);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'email' => ['required'],
            'role_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        $user = User::find($id);
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $role = Role::find($request->input('role_id'));
        $role->users()->save($user);
        return response()->json($user, 200);
    }

    /**
     * Redefine the user password
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function redefinePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        $user = User::find($id);
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(null, 204);
    }

}
