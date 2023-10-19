@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Название"
                       value="{{old('name', $physical_exercise->name)}}">
            </div>
            <div class="mb-3">
                    <textarea name="description" class="form-control js-auto-size textarea-no-scrollbar"
                              placeholder="Описание">{{old('description', $physical_exercise->description)}}</textarea>
            </div>
            <div class="mb-3">
                <select id="pe-edit" @if ($physical_exercise->moderated_by) disabled @endif required name="status"
                        class="form-control select2 without-search @error('status') is-invalid @enderror">
                    <option value="" disabled selected>Статус</option>
                    <option value="1"
                            @if(old('status', $physical_exercise->status) == PhysicalExercise::STATUS_PRIVATE) selected @endif>{{physicalExerciseIntToName(PhysicalExercise::STATUS_PRIVATE)}}</option>
                    <option value="2"
                            @if(old('status', $physical_exercise->status) == PhysicalExercise::STATUS_IN_MODERATION) selected @endif>{{physicalExerciseIntToName(PhysicalExercise::STATUS_IN_MODERATION)}}</option>
                </select>
            </div>
            <div class="mb-3 text-left">
                <button type="submit" class="btn btn-primary w-100">Сохранить</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            document.querySelector('button[type="submit"]')?.addEventListener('click', (event) => {
                let data = {};
                let id = {{$physical_exercise->id}};
                data.name = document.querySelector('input[name="name"]').value;
                data.description = document.querySelector('textarea[name="description"]').innerHTML;
                data.status = document.getElementById('pe-edit').value;

                fetch('/settings/physical-exercises/' + id, {
                    method: 'PUT',
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
