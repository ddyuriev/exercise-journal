import "bootstrap";
import $ from 'jquery';

window.$ = window.jQuery = $;
import select2 from 'select2';

select2();

$('.select2')?.select2({
    width: 'resolve',
    theme: 'bootstrap-5',
})

//Добавление user_physical_exercises через select2
$('#select-physical_exercise').on('select2:select', function (e) {
    createUserPhysicalExercise(e.target.value);
    $('#select-physical_exercise').val(0).trigger('change');
});

function createUserPhysicalExercise(value) {
    const formData = new FormData();
    formData.append('physicalExerciseId', value);
    //url params
    const queryString = window.location.search;
    formData.append('queryString', queryString);
    formData.append('date', window.location.pathname.replace("/day/", ""));

    fetch('/day/user-physical-exercises', {
        method: 'POST',
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "X-Requested-With": "XMLHttpRequest"
        }
    }).then(response => {
        return response.json();
    }).then(data => {
        console.log(data);
        if (data.is_success) {
            if (data.is_need_reload) {
                location.reload();
            } else {
                document.getElementById('intradaily-exercises-body').remove();
                drawUserPhysicalExerciseTable(data.items);
                updateUserPhysicalExercise();
                deleteUserPhysicalExercise();
            }
        }
    });
}

// document.getElementsByClassName('calendar-table-body')[0]?.addEventListener('click', function (event) {
//     console.log(event.target.id);
//
//     // document.getElementsByClassName('calendar-table')[0].style.display = 'none';
//
//     document.getElementById('calendar').style.display = 'none';
//     document.getElementById('calendar-details').style.display = 'block';
//
// });


// document.getElementById('calendar-details')?.addEventListener('click', function (event) {
//     document.getElementById('calendar').style.display = 'block';
//     document.getElementById('calendar-details').style.display = 'none';
//
//     const formData = new FormData();
//     formData.append('111', 112);
//
//     fetch('/update', {
//         method: 'POST',
//         body: formData,
//         headers: {
//             "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//         }
//     }).then(response => {
//         return response.json();
//     }).then(data => {
//         console.log(data);
//
//         if (data.items) {
//         }
//     });
// });


// document.getElementsByClassName('btn-confirm-recalculate')[0]
//     .addEventListener('click', function (event) {
//
//         event.preventDefault();
//         const formData = new FormData();
//         formData.append('111', 112);
//
//         fetch('/update', {
//             method: 'POST',
//             body: formData,
//             headers: {
//                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//             }
//         }).then(response => {
//             return response.json();
//         }).then(data => {
//             console.log(data);
//             if (data.items) {
//             }
//         });
//     })


//settings


function settingsSelectPhysicalExercisesEL() {
    let formPhysicalExercisesToggles = document.getElementsByClassName('form-physical-exercises-toggle');
    for (const [key, formPhysicalExercisesToggle] of Object.entries(formPhysicalExercisesToggles)) {
        formPhysicalExercisesToggle.addEventListener('submit', function (event) {
            event.preventDefault();
            let physicalExerciseId = event.target.id.replace('pe-toggle-', '');

            const formData = new FormData();
            formData.append('physicalExerciseId', physicalExerciseId);

            //url params
            const queryString = window.location.search;
            console.log(queryString);

            formData.append('queryString', queryString);

            fetch('/settings/physical-exercises/toggle', {
                method: 'POST',
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                console.log(data);
                if (data.is_success) {
                    //tboby
                    document.querySelector('#physical-exercises-settings table tbody').remove();
                    tableSelectPhysicalExercisesCreate(data);
                    settingsSelectPhysicalExercisesEL();


                    // window.history.pushState("Details", "Title", "settings?page=" + data.items.physical_exercises.current_page);
                    window.history.pushState("Details", "Title", "physical-exercises?page=" + data.items.physical_exercises.current_page);
                }
            });
        })
    }
}

settingsSelectPhysicalExercisesEL();

function tableSelectPhysicalExercisesCreate(data) {
    const parent = document.querySelector('#physical-exercises-settings table');
    let tb = parent.createTBody();
    for (const [key, datum] of Object.entries(data.items.physical_exercises.data)) {
        const tr = tb.insertRow();
        tr.setAttribute("id", "tr-" + datum.id);
        if (datum.users_count !== 1) {
            tr.classList.add("physical-exercises-unselected");
        }

        let td = tr.insertCell();
        td.appendChild(document.createTextNode(datum.name));

        td = tr.insertCell();
        td.appendChild(document.createTextNode(datum.description ?? ''));

        td = tr.insertCell();
        td.appendChild(document.createTextNode(''));
        td.classList.add("action-icons");
        td.classList.add("text-center");
        let togglePosition = datum.users_count === 1 ? 'bi-toggle2-on' : 'bi-toggle2-off';
        td.innerHTML = `
            <form method="POST" id="pe-toggle-${datum.id}" class="form-physical-exercises-toggle" action="/settings/physical-exercises/toggle">
                <button class="btn btn-grow btn-confirm-recalculate">
                    <i class="bi ${togglePosition}"></i>
                </button>
            </form>
        `;
    }
    parent.appendChild(tb);
}


//Таблица выполненных упражнений внутри дня
function updateUserPhysicalExercise() {
    let inputs = document.querySelectorAll("#intradaily-exercises input");
    for (const [key, input] of Object.entries(inputs)) {
        input.addEventListener('change', function (event) {

            console.log(event.target)

            let data = {};
            let id = 0;
            let inputNameAttr = event.target.getAttribute('name');

            if (event.target.classList.contains('item-count')) {
                id = inputNameAttr.replace("pe-count-", "");
                data.count = event.target.value;
            } else if (event.target.classList.contains('item-comment')) {
                id = inputNameAttr.replace("pe-comment-", "");
                data.comment = event.target.value;
            }

            data.date = window.location.pathname.replace("/day/", "");
            data.queryString = window.location.search;

            fetch('/day/user-physical-exercises/' + id, {
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
                    document.getElementById('intradaily-exercises-body').remove();
                    drawUserPhysicalExerciseTable(data.items);
                    updateUserPhysicalExercise();
                    deleteUserPhysicalExercise();
                }
            });
        });
    }
}

updateUserPhysicalExercise();


function deleteUserPhysicalExercise() {
    let deleteControls = document.querySelectorAll("#intradaily-exercises .delete-control i");
    for (const [key, control] of Object.entries(deleteControls)) {
        control.addEventListener('click', function (event) {

            console.log(event.target);
            // console.log(event.target.querySelector('i'));

            // let subelement = event.target.querySelector('i');
            // console.log(subelement);

            let id = event.target.id.replace("i-element-", "");

            console.log(id);

            let data = {};
            data.date = window.location.pathname.replace("/day/", "");
            data.queryString = window.location.search;

            fetch('/day/user-physical-exercises/' + id, {
                method: 'DELETE',
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
                    // console.log('draw');

                    if (data.is_need_reload) {
                        location.reload();
                    } else {
                        document.getElementById('intradaily-exercises-body').remove();
                        drawUserPhysicalExerciseTable(data.items);
                        updateUserPhysicalExercise();
                        deleteUserPhysicalExercise();
                    }
                }
            });

        });
    }
}

deleteUserPhysicalExercise();


function drawUserPhysicalExerciseTable(data) {
    // console.log('drawUserPhysicalExerciseTable- data');
    // console.log(data);

    const intradailyExercises = document.getElementById('intradaily-exercises');
    const intradailyExercisesBody = document.createElement('div');
    intradailyExercisesBody.id = 'intradaily-exercises-body';
    intradailyExercises.insertAdjacentElement('beforeend', intradailyExercisesBody);

    for (const [key, datum] of Object.entries(data)) {

        // console.log('datum');
        // console.log(datum);

        let element = document.createElement('div');
        element.setAttribute('draggable', true);
        element.classList.add("row");
        element.classList.add("block-body");

        let subelementNumber = document.createElement('div');
        subelementNumber.classList.add("text-start");
        subelementNumber.classList.add("sort-field");
        subelementNumber.classList.add("physical-exercise-" + datum.id);
        subelementNumber.innerHTML = datum.intraday_key;

        let subelementName = document.createElement('div');
        subelementName.classList.add("col-3");
        subelementName.classList.add("text-start");
        subelementName.classList.add("physical-exercise-" + datum.id);
        let subelementNameSpan = document.createElement('span');
        subelementNameSpan.innerHTML = datum.physical_exercises.name;
        subelementName.insertAdjacentElement('beforeend', subelementNameSpan);

        let subelementCount = document.createElement('div');
        subelementCount.classList.add("col-2");
        let subelementCountDiv = document.createElement('div');
        subelementCountDiv.classList.add("input-parent");
        subelementCountDiv.classList.add("border-bottom");
        subelementCount.insertAdjacentElement('afterbegin', subelementCountDiv);
        let subelementCountInput = document.createElement('input');
        subelementCountInput.classList.add("item-count");
        subelementCountInput.type = "text";
        subelementCountInput.setAttribute('autocomplete', "none");
        subelementCountInput.setAttribute('name', "pe-count-" + datum.id);
        subelementCountInput.value = datum.count;
        subelementCountDiv.insertAdjacentElement('afterbegin', subelementCountInput);

        let subelementComment = document.createElement('div');
        subelementComment.classList.add("col");
        let subelementCommentDiv = document.createElement('div');
        subelementCommentDiv.classList.add("input-parent");
        subelementCommentDiv.classList.add("border-bottom");
        subelementComment.insertAdjacentElement('afterbegin', subelementCommentDiv);
        let subelemenCommentInput = document.createElement('input');
        subelemenCommentInput.classList.add("item-comment");
        subelemenCommentInput.type = "text";
        subelemenCommentInput.setAttribute('autocomplete', "none");
        subelemenCommentInput.setAttribute('name', "pe-comment-" + datum.id);
        subelemenCommentInput.value = datum.comment;
        subelementCommentDiv.insertAdjacentElement('afterbegin', subelemenCommentInput);

        let subelementControl = document.createElement('div');
        subelementControl.classList.add("col-1");
        subelementControl.classList.add("delete-control");
        let subelementControlDiv = document.createElement('div');
        subelementControlDiv.classList.add("w-50");
        subelementControl.insertAdjacentElement('afterbegin', subelementControlDiv);
        let subelemenControlI = document.createElement('i');
        subelemenControlI.id = datum.id;
        subelemenControlI.classList.add("bi");
        subelemenControlI.classList.add("bi-x");
        subelementControlDiv.insertAdjacentElement('afterbegin', subelemenControlI);


        element.insertAdjacentElement('afterbegin', subelementControl);
        element.insertAdjacentElement('afterbegin', subelementComment);
        element.insertAdjacentElement('afterbegin', subelementCount);
        element.insertAdjacentElement('afterbegin', subelementName);
        element.insertAdjacentElement('afterbegin', subelementNumber);

        intradailyExercisesBody.insertAdjacentElement('beforeend', element);
    }
}


//profile block toggle (PC)

let isProfileVisible = 0;

window.addEventListener('click', function (event) {
    if (!document.getElementById('profile-icon').contains(event.target) && isProfileVisible) {
        document.getElementById("pc-profile-widget").style.cssText += `
animation: h1de 0.3s forwards;
            `;
        setTimeout(function () {
            document.getElementById("pc-profile-widget").style.cssText += `
visibility: hidden;
            `;
        }, 300);

        isProfileVisible = 0;
    }
});

document.getElementById('profile-icon')?.addEventListener('click', function (event) {
    console.log(event.target.id);

    if (isProfileVisible === 0) {
        document.getElementById("pc-profile-widget").style.cssText = `
visibility: visible;
animation: show 0.8s forwards;
        `;
        isProfileVisible = 1;
    }
});
