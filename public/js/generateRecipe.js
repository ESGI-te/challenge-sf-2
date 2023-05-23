const $ingredientsInputs = document.querySelectorAll('.ingredients');
const $durationInput = document.querySelector('.duration');
const $difficultyInput = document.querySelector('.difficulty');
const $incrementButton = document.getElementById('increment_nb_people');
const $decrementButton = document.getElementById('decrement_nb_people');
const $nbPeopleInput = document.getElementById('recipe_nb_people');

$incrementButton.addEventListener('click', (e) => {
    e.preventDefault();
    $nbPeopleInput.stepUp();
});

$decrementButton.addEventListener('click', (e) => {
    e.preventDefault();
    $nbPeopleInput.stepDown();
});

$ingredientsInputs.forEach((el)=>{
    let settings = {
        sortField: {
            field: "text",
            direction: "asc"
        }
    };
    new TomSelect(el, settings);
});

new TomSelect($durationInput,{
    create: true,
    sortField: {
        field: "text",
        direction: "asc"
    }
});

new TomSelect($difficultyInput,{
    create: true,
    sortField: {
        field: "text",
        direction: "asc"
    }
});
