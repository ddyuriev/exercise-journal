import 'bootstrap';

import $ from 'jquery';

window.$ = window.jQuery = $;

import select2 from 'select2';

select2();
$('.select2')?.select2({
    width: 'resolve',
    theme: 'bootstrap-5',
})

import {TextareaAutoSize} from 'textarea-autosize'

let wrapper = [];
let textareasPET = document.querySelectorAll('textarea.js-auto-size');
if (Object.keys(textareasPET).length) {
    for (const [key, textareaPET] of Object.entries(textareasPET)) {
        wrapper.push(new TextareaAutoSize(textareaPET));
    }
}

import Chart from 'chart.js/auto';

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
                window.location = window.location.origin + window.location.pathname + '?page=' + data.page_correction;
            } else {
                UserPhysicalExerciseUpdateDOM(data.items);
            }
        }
    });
}


//settings

function settingsTogglePhysicalExercisesEL() {
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
                    drawSelectPhysicalExercisesTable(data.items.physical_exercises.data);
                    settingsTogglePhysicalExercisesEL();
                    window.history.pushState('', '', 'physical-exercises' + queryString);
                }
            });
        })
    }
}

settingsTogglePhysicalExercisesEL();

function drawSelectPhysicalExercisesTable(data) {

    console.log('data');
    console.log(data);

    const parent = document.querySelector('#physical-exercises-settings table');
    let tb = parent.createTBody();
    for (const [key, datum] of Object.entries(data)) {
        const tr = tb.insertRow();
        tr.setAttribute("id", "tr-" + datum.id);
        if (!datum.user_id) {
            tr.classList.add("physical-exercises-unselected");
        }

        let td = tr.insertCell();
        // td.appendChild(document.createTextNode(datum.name));

        if (datum.status == 1) {
            td.appendChild(document.createTextNode(datum.private_name));
            td.classList.add("name-private");
        } else {
            td.appendChild(document.createTextNode(datum.name));
            if((datum.status == 3)){
                td.classList.add("name-confirmed");
            }
        }

        td = tr.insertCell();
        td.appendChild(document.createTextNode(datum.description ?? ''));

        td = tr.insertCell();
        td.appendChild(document.createTextNode(''));
        td.classList.add("action-icons");
        td.classList.add("text-center");

        let togglePosition = datum.user_id ? 'bi-toggle2-on' : 'bi-toggle2-off';
        td.innerHTML = `
            <form method="POST" id="pe-toggle-${datum.id}" class="form-physical-exercises-toggle float-start" action="/settings/physical-exercises/toggle">
                <button class="btn btn-grow btn-confirm-recalculate">
                    <i class="bi ${togglePosition}"></i>
                </button>
            </form>
        `;
        if (datum.created_by == datum.user_id) {
            td.innerHTML += `
                <a href="/settings/physical-exercises/${datum.id}/edit"
                   class="btn">
                    <i class="bi bi bi-pen"></i>
                </a>
            `;

        }
    }
    parent.appendChild(tb);
}

let physicalExercisesSettingsSearchInput = document.querySelector('#physical-exercises-settings .search-input');
let searchPrev = '';
physicalExercisesSettingsSearchInput?.addEventListener('keyup', (event) => {
    let search = physicalExercisesSettingsSearchInput.value;
    if ((searchPrev !== search) && ((search === '') || (search.length > 1))) {
        searchPrev = search;
        fetch('/settings/physical-exercises/search?' + 'name=' + search, {
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
                document.querySelector('#physical-exercises-settings table tbody').remove();
                drawSelectPhysicalExercisesTable(data.items.physical_exercises.data);
                settingsTogglePhysicalExercisesEL();
                window.history.pushState("Details", "Title", "physical-exercises?name=" + search);
                document.querySelector('.common-pagination').innerHTML = data.pagination;
            }
        });
    }
});

//create by user


document.querySelector('.physical-exercises-settings-header i')?.addEventListener('click', function (event) {
    console.log(event.target);
})


//Таблица выполненных упражнений внутри дня
function updateUserPhysicalExercise() {
    let inputs = document.querySelectorAll("div[id^=intradaily-exercises] .item-count, div[id^=intradaily-exercises] .item-comment");
    for (const [key, input] of Object.entries(inputs)) {
        input.addEventListener('change', function (event) {
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
                    UserPhysicalExerciseUpdateDOM(data.items);
                }
            });
        });
    }
}

updateUserPhysicalExercise();

function deleteUserPhysicalExercise() {
    let deleteControls = document.querySelectorAll("div[id^=intradaily-exercises] .delete-control i");
    for (const [key, control] of Object.entries(deleteControls)) {
        control.addEventListener('click', function (event) {
            let id = event.target.id.replace("i-element-", "");
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
                    if (data.is_need_reload) {
                        window.location = window.location.origin + window.location.pathname + '?page=' + data.page_correction;
                    } else {
                        UserPhysicalExerciseUpdateDOM(data.items);
                    }
                }
            });

        });
    }
}

deleteUserPhysicalExercise();


function drawUserPhysicalExerciseTable(data) {
    const intradailyExercises = document.getElementById('intradaily-exercises');
    const intradailyExercisesBody = document.createElement('div');
    intradailyExercisesBody.id = 'intradaily-exercises-body';
    intradailyExercises.insertAdjacentElement('beforeend', intradailyExercisesBody);

    const intradailyExercisesLowRes = document.getElementById('intradaily-exercises-low-res');
    const intradailyExercisesBodyLowRes = document.createElement('div');
    intradailyExercisesBodyLowRes.id = 'intradaily-exercises-body-low-res';
    intradailyExercisesLowRes.insertAdjacentElement('beforeend', intradailyExercisesBodyLowRes);

    for (const [key, datum] of Object.entries(data)) {
        let element = document.createElement('div');
        element.setAttribute('draggable', true);
        element.classList.add("row", "block-body", "mt-2");

        let subelementNumber = document.createElement('div');
        subelementNumber.classList.add("col-05-cstm", "text-start", "physical-exercise-" + datum.id);
        subelementNumber.innerHTML = datum.intraday_key;

        let subelementName = document.createElement('div');
        subelementName.classList.add("col-35-cstm", "text-start", "physical-exercise-" + datum.id);
        let subelementNameSpan = document.createElement('span');
        subelementNameSpan.innerHTML = datum.physical_exercises.name;
        subelementName.insertAdjacentElement('beforeend', subelementNameSpan);

        let subelementCount = document.createElement('div');
        subelementCount.classList.add("col-2");
        let subelementCountDiv = document.createElement('div');
        subelementCountDiv.classList.add("border-bottom-hover", "border-bottom");
        subelementCount.insertAdjacentElement('afterbegin', subelementCountDiv);
        let subelementCountInput = document.createElement('input');
        subelementCountInput.classList.add("item-count");
        subelementCountInput.type = "number";
        subelementCountInput.setAttribute('autocomplete', "none");
        subelementCountInput.setAttribute('name', "pe-count-" + datum.id);
        subelementCountInput.value = datum.count;
        subelementCountDiv.insertAdjacentElement('afterbegin', subelementCountInput);

        let subelementComment = document.createElement('div');
        subelementComment.classList.add("col-5");
        let subelementCommentDiv = document.createElement('div');
        subelementCommentDiv.classList.add("border-bottom-hover", "border-bottom");
        subelementComment.insertAdjacentElement('afterbegin', subelementCommentDiv);

        let subelementCommentTextArea = document.createElement('textarea');
        subelementCommentTextArea.classList.add("item-comment", "js-auto-size", "color-gray");
        subelementCommentTextArea.setAttribute('name', "pe-comment-" + datum.id);
        subelementCommentTextArea.innerHTML = datum.comment;
        subelementCommentDiv.insertAdjacentElement('afterbegin', subelementCommentTextArea);

        let subelementControl = document.createElement('div');
        subelementControl.classList.add("col-1", "delete-control");
        let subelementControlDiv = document.createElement('div');
        subelementControlDiv.classList.add("h5", "position-relative");
        subelementControl.insertAdjacentElement('afterbegin', subelementControlDiv);
        let subelemenControlI = document.createElement('i');
        subelemenControlI.id = datum.id;
        subelemenControlI.classList.add("bi", "bi-x", "position-absolute", "end-0");

        subelementControlDiv.insertAdjacentElement('afterbegin', subelemenControlI);


        element.insertAdjacentElement('afterbegin', subelementControl);
        element.insertAdjacentElement('afterbegin', subelementComment);
        element.insertAdjacentElement('afterbegin', subelementCount);
        element.insertAdjacentElement('afterbegin', subelementName);
        element.insertAdjacentElement('afterbegin', subelementNumber);

        intradailyExercisesBody.insertAdjacentElement('beforeend', element);

        let elementLR = document.createElement('div');
        elementLR.setAttribute('draggable', true);
        elementLR.classList.add("row", "block-body", "mt-4");

        let subelementLRName = document.createElement('div');
        subelementLRName.classList.add("col-8", "text-start", "physical-exercise-" + datum.id);
        let subelementLRNameSpan = document.createElement('span');
        subelementLRNameSpan.classList.add("color-goldenrod");
        subelementLRNameSpan.innerHTML = datum.physical_exercises.name;
        subelementLRName.insertAdjacentElement('beforeend', subelementLRNameSpan);

        let subelementLRCount = document.createElement('div');
        subelementLRCount.classList.add("col-2");
        let subelementLRCountDiv = document.createElement('div');
        subelementLRCountDiv.classList.add("border-bottom-hover", "border-bottom");
        let subelementLRCountInput = document.createElement('input');
        subelementLRCountInput.classList.add("item-count", "text-center");
        subelementLRCountInput.type = "number";
        subelementLRCountInput.setAttribute('autocomplete', "none");
        subelementLRCountInput.setAttribute('name', "pe-count-" + datum.id);
        subelementLRCountInput.value = datum.count;
        subelementLRCountDiv.insertAdjacentElement('afterbegin', subelementLRCountInput);
        subelementLRCount.insertAdjacentElement('afterbegin', subelementLRCountDiv);

        let subelementLRControl = document.createElement('div');
        subelementLRControl.classList.add("col-2");
        let subelementLRControlDiv = document.createElement('div');
        subelementLRControlDiv.classList.add("delete-control", "text-center");
        let subelementLRControlI = document.createElement('i');
        subelementLRControlI.id = datum.id;
        subelementLRControlI.classList.add("bi", "bi-x");
        subelementLRControlDiv.insertAdjacentElement('afterbegin', subelementLRControlI);
        subelementLRControl.insertAdjacentElement('afterbegin', subelementLRControlDiv);

        elementLR.insertAdjacentElement('afterbegin', subelementLRControl);
        elementLR.insertAdjacentElement('afterbegin', subelementLRCount);
        elementLR.insertAdjacentElement('afterbegin', subelementLRName);

        let elementLRSecondLine = document.createElement('div');
        elementLRSecondLine.classList.add("row", "block-body");

        let elementLRSecondLineDiv = document.createElement('div');
        elementLRSecondLineDiv.classList.add("col-12");
        let elementLRSecondLineDivDiv = document.createElement('div');
        elementLRSecondLineDivDiv.classList.add("border-bottom-hover", "border-bottom");
        let elementLRSecondLineTextArea = document.createElement('textarea');
        elementLRSecondLineTextArea.classList.add("item-comment", "js-auto-size", "color-gray");
        elementLRSecondLineTextArea.setAttribute('name', "pe-comment-" + datum.id);
        elementLRSecondLineTextArea.innerHTML = datum.comment;
        elementLRSecondLineDivDiv.insertAdjacentElement('afterbegin', elementLRSecondLineTextArea);
        elementLRSecondLineDiv.insertAdjacentElement('afterbegin', elementLRSecondLineDivDiv);
        elementLRSecondLine.insertAdjacentElement('afterbegin', elementLRSecondLineDiv);

        intradailyExercisesBodyLowRes.insertAdjacentElement('beforeend', elementLR);
        intradailyExercisesBodyLowRes.insertAdjacentElement('beforeend', elementLRSecondLine);
    }
}

function UserPhysicalExerciseUpdateDOM(items) {
    document.getElementById('intradaily-exercises-body').remove();
    document.getElementById('intradaily-exercises-body-low-res').remove();

    drawUserPhysicalExerciseTable(items);
    updateUserPhysicalExercise();
    deleteUserPhysicalExercise();

    //since i remove the elements, we need to redefine wrapper
    let textareasPET = document.querySelectorAll('textarea.js-auto-size');
    wrapper = [];
    for (const [key, textareaPET] of Object.entries(textareasPET)) {
        wrapper.push(new TextareaAutoSize(textareaPET));
    }
}


//profile block toggle (PC)

let isProfileVisible = 0;

window.addEventListener('click', function (event) {
    let profileIcon = document.getElementById('profile-icon');
    if (profileIcon && !profileIcon.contains(event.target) && isProfileVisible) {
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

//textarea auto-size
window.addEventListener('resize', () => {
    //if we update wrapped elements only when switch between tables, the auto-size textarea component does not always work as expected.
    //So we have to do this for every resize
    for (let wrapperItem of wrapper) {
        wrapperItem.update();
    }
});

//month picker
let monthPickerInput = document.querySelector('.month-picker');

monthPickerInput?.addEventListener('change', (event) => {
    console.log(event.target.value);
    console.log(window.location);

    window.location.replace(window.location.origin + '?year-month=' + event.target.value);
});

//chart
const ctx = document.getElementById('statistics-chart');
let statisticChart;

if (ctx) {
    drawChart(statisticsData.keysPeriod, statisticsData.statistics, statisticsData.colors);
}

$('#statistics-select').on('select2:select', function (e) {
    fetch('/statistics' + '?period=' + e.target.value, {
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
            if (statisticChart) statisticChart.destroy();
            drawChart(data.data.keysPeriod, data.data.statistics, data.data.colors);
            window.history.pushState("Details", "Title", "statistics?period=" + e.target.value);
        }
    });

});


function drawChart(labelsObj, statisticsObj, colorsObj) {
    let labels = Object.keys(labelsObj);
    let datasets = [];
    for (const [key, value] of Object.entries(statisticsObj)) {
        datasets.push({
            'label': key,
            'data': statisticsObj[key],
            'borderWidth': 2,
            'borderColor': colorsObj.key
        });
    }
    statisticChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

//remove search field
$(".without-search").select2({
    minimumResultsForSearch: Infinity
});
