@extends('layouts.app')
@section('titlepage', 'Roles')

@section('content')
@section('navigasi')
    <span class="text-muted fw-light">Roles</span> / Set Role Permission {{ ucwords($role->name) }}
@endsection
<form action="{{ route('roles.storerolepermission', Crypt::encrypt($role->id)) }}" method="POST">
    @csrf
    <style>
        .masonry-container {
            column-count: 1;
            column-gap: 1.5rem;
        }

        .masonry-item {
            display: inline-block;
            width: 100%;
            break-inside: avoid;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 576px) {
            .masonry-container {
                column-count: 2;
            }
        }

        @media (min-width: 768px) {
            .masonry-container {
                column-count: 3;
            }
        }

        @media (min-width: 1200px) {
            .masonry-container {
                column-count: 4;
            }
        }
        
        /* Optional: Make cards look cleaner */
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 1rem;
            padding: 0.75rem 1.25rem;
        }
        
        .form-check-label {
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .form-check {
            margin-bottom: 0.25rem;
        }
    </style>

    <div class="masonry-container">
        @php
            $id_permission_group = '';
        @endphp
        @foreach ($permissions as $key => $d)
            <div class="masonry-item">
                <div class="card border mb-0 shadow-sm">
                    <div class="card-header border-bottom">
                        {{ $d->group_name }}
                    </div>
                    <div class="card-body">
                        @php
                            $list_permissions = explode(',', $d->permissions);
                        @endphp
                        @foreach ($list_permissions as $p)
                            @php
                                $permission = explode('-', $p);
                                $permission_id = $permission[0];
                                $permission_name = $permission[1];
                                $cek = in_array($permission_name, $rolepermissions);
                            @endphp
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permission[]"
                                    value="{{ $permission_name }}" id="defaultCheck{{ $permission_id }}"
                                    {{ $cek > 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck{{ $permission_id }}">
                                    {{ $permission_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @php
                $id_permission_group = $d->id_permission_group;
            @endphp
        @endforeach
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-primary w-100">
                <ion-icon name="repeat-outline" class="me-1"></ion-icon>
                Update
            </button>
        </div>
    </div>
</form>
@endsection
