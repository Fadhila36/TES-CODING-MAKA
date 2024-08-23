<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%");
        }

        $users = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $users,
            'message' => 'Users fetched successfully'
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->only(['name', 'address']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload($request->file('image'));
        }

        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
            'message' => 'User fetched successfully'
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $this->deleteOldImage($user->image);
            $data['image'] = $this->handleImageUpload($request->file('image'));
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $this->deleteOldImage($user->image);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }

    private function handleImageUpload($image)
    {
        $fileName = 'user_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('images', $fileName);
    }

    private function deleteOldImage($path)
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
