@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="mb-3">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       placeholder="Название" value="{{old('name')}}">
                @error('name')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
            <div class="mb-3">
                <textarea name="description" class="form-control" placeholder="Описание"></textarea>
            </div>

            <div class="mb-3">
                <select id="pe-edit" required name="status"
                        class="form-control select2 without-search @error('status') is-invalid @enderror">
                    <option value="" disabled selected>Статус</option>
                    <option value="1" @if(old('status') == 1) selected @endif>Приватное</option>
                    <option value="2" @if(old('status') == 2) selected @endif>Общее</option>
                </select>
                @error('status')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>

            <div class="mb-3 text-left">
                <button type="submit" class="btn btn-primary w-100">Создать</button>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            document.querySelector('button[type="submit"]')?.addEventListener('click', (event) => {
                let data = {};
                data.name = document.querySelector('input[name="name"]').value;
                data.description = document.querySelector('textarea[name="description"]').innerHTML;
                data.status = document.getElementById('pe-edit').value;

                fetch('/settings/physical-exercises', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }).then(response => {
                    return response.json();
                }).then(data => {
                    console.log(data);
                    if (data.is_success) {
                        toastifyNotification('success', 'сохранено');
                    } else {
                        console.log(data.errors);
                        for (const [key, val] of Object.entries(data.errors)) {
                            toastifyNotification('error', val);
                        }
                    }
                });
            });

        });
    </script>

@endsection
