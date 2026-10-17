@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Team</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Team</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="d-flex flex-wrap justify-content-between gap-2">
                    <div class="search-bar">
                        <span><i class="bx bx-search-alt"></i></span>
                        <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, jabatan..." />
                    </div>

                    <a href="{{ route('team.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Anggota
                    </a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-centered text-nowrap mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Jabatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamMembers as $member)
                            <tr>
                                <td>{{ $member['name'] }}</td>
                                <td>{{ $member['email'] }}</td>
                                <td>{{ $member['whatsapp'] }}</td>
                                <td>{{ $member['role'] }}</td>
                                <td class="text-end">
                                    <a href="{{ route('team.edit', $member['id']) }}" class="btn btn-sm btn-soft-primary me-1">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('team.destroy', $member['id']) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Hapus anggota tim ini?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada anggota tim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
