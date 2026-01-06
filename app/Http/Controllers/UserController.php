<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function user_index(Request $request)
    {
        $data_users = User::query();

        // Search by name or email
        if ($request->filled('cari_nama')) {
            $data_users->where(function($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->cari_nama . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->cari_nama . '%');
            });
        }

        // Filter by role
        if ($request->filled('cari_role')) {
            $data_users->where('role', $request->cari_role);
        }

        // Pagination
        $data_users = $data_users->orderBy('created_at', 'desc')
                                 ->paginate($request->get('set_pagination', 10));

        // Statistics
        $total_admin = User::where('role', 'admin')->count();
        $total_staff = User::where('role', 'staff')->count();

        // Preserve filters
        $cari_nama = $request->cari_nama;
        $cari_role = $request->cari_role;

        return view('user.index', compact(
            'data_users',
            'total_admin',
            'total_staff',
            'cari_nama',
            'cari_role'
        ));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function user_create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function user_proses_simpan(Request $request)
    {
        // Validation rules
        $rule_validasi = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:admin,staff',
        ];

        // Custom validation messages (Indonesian)
        $pesan_validasi = [
            'name.required'         => 'Nama Harus di Isi !',
            'name.string'           => 'Nama Harus Berupa Text !',
            'name.max'              => 'Nama Maksimal 255 Karakter !',
            'email.required'        => 'Email Harus di Isi !',
            'email.email'           => 'Email Tidak Valid !',
            'email.unique'          => 'Email Sudah Terdaftar !',
            'password.required'     => 'Password Harus di Isi !',
            'password.string'       => 'Password Harus Berupa Text !',
            'password.min'          => 'Password Minimal 8 Karakter !',
            'password.confirmed'    => 'Konfirmasi Password Tidak Cocok !',
            'role.required'         => 'Role Harus di Pilih !',
            'role.in'               => 'Role Tidak Valid !',
        ];

        // Validate request
        $request->validate($rule_validasi, $pesan_validasi);

        // Create new user
        $data_user = new User();
        $data_user->name = $request->name;
        $data_user->email = $request->email;
        $data_user->password = Hash::make($request->password);
        $data_user->role = $request->role;
        $data_user->email_verified_at = Carbon::now(); // Auto-verify admin-created users
        $data_user->save();

        return redirect()->route('get.user.index')->with('pesan', 'User Berhasil Ditambahkan !');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function user_edit($id)
    {
        $data_user = User::findOrFail($id);

        return view('user.edit', compact('data_user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function user_proses_ubah(Request $request, $id)
    {
        $data_user = User::findOrFail($id);

        // Self-protection: Prevent admin from changing their own role
        if (auth()->id() === $data_user->id && $request->role !== 'admin') {
            return back()->with('kesalahan', 'Anda tidak dapat mengubah role Anda sendiri !');
        }

        // Validation rules
        $rule_validasi = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'password'  => 'nullable|string|min:8|confirmed',
            'role'      => 'required|in:admin,staff',
        ];

        // Custom validation messages (Indonesian)
        $pesan_validasi = [
            'name.required'         => 'Nama Harus di Isi !',
            'name.string'           => 'Nama Harus Berupa Text !',
            'name.max'              => 'Nama Maksimal 255 Karakter !',
            'email.required'        => 'Email Harus di Isi !',
            'email.email'           => 'Email Tidak Valid !',
            'email.unique'          => 'Email Sudah Terdaftar !',
            'password.string'       => 'Password Harus Berupa Text !',
            'password.min'          => 'Password Minimal 8 Karakter !',
            'password.confirmed'    => 'Konfirmasi Password Tidak Cocok !',
            'role.required'         => 'Role Harus di Pilih !',
            'role.in'               => 'Role Tidak Valid !',
        ];

        // Validate request
        $request->validate($rule_validasi, $pesan_validasi);

        // Update user data
        $data_user->name = $request->name;
        $data_user->email = $request->email;
        $data_user->role = $request->role;

        // Only update password if provided
        if ($request->filled('password')) {
            $data_user->password = Hash::make($request->password);
        }

        $data_user->save();

        return back()->with('pesan', 'User Berhasil Diupdate !');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function user_proses_hapus($id)
    {
        $data_user = User::findOrFail($id);

        // Self-protection: Prevent admin from deleting themselves
        if (auth()->id() === $data_user->id) {
            return back()->with('kesalahan', 'Anda tidak dapat menghapus akun Anda sendiri !');
        }

        $data_user->delete();

        return back()->with('pesan', 'User Berhasil Dihapus !');
    }
}
