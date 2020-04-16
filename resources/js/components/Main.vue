<template>
    <form @submit.prevent="startProcessing()">
        <p v-if="errors.length">
            <b>Prosz poprawić nastęþujące błędy:</b>
        <ul>
            <li v-for="error in errors">{{ error }}</li>
        </ul>
        </p>
        <div class="form-group">
            <label for="voivodeship-input">Wybierz województwo</label>
            <select class="form-control" id="voivodeship-input" name="voivodeship" required="" v-model="voivodeship">
                <option value="dolnośląskie">dolnośląskie</option>
                <option value="kujawsko-pomorskie">kujawsko-pomorskie</option>
                <option value="lubelskie">lubelskie</option>
                <option value="lubuskie">lubuskie</option>
                <option value="łódzkie">łódzkie</option>
                <option value="małopolskie">małopolskie</option>
                <option value="mazowieckie">mazowieckie</option>
                <option value="opolskie">opolskie</option>
                <option value="podkarpackie">podkarpackie</option>
                <option value="podlaskie">podlaskie</option>
                <option value="pomorskie">pomorskie</option>
                <option value="śląskie">śląskie</option>
                <option value="świętokrzyskie">świętokrzyskie</option>
                <option value="warmińsko-mazurskie">warmińsko-mazurskie</option>
                <option value="wielkopolskie">wielkopolskie</option>
                <option value="zachodniopomorskie">zachodniopomorskie</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email-input">Email address</label>
            <input type="email" class="form-control" id="email-input" placeholder="xxx@xxx.com.pl" required="" 
                   v-model="email">
        </div>
        <div class="form-group">
            <label for="start-date-input">Data początkowa</label>
            <input type="date" id="start-date-input" class="form-control" required="" v-model="startDate">
        </div>
        <div class="form-group">
            <label for="end-date-input">Data końcowa</label>
            <input type="date" id="end-date-input" class="form-control" required="" v-model="endDate">
        </div>
        <div class="form-group">
            <button type="submit">Start proccesing</button>
        </div>
    </form>
</template>

<script>
    export default {
        data() {
            console.log('data entered');
            return{
                minDate: '01-01-1950',
                maxDate: '31-12-2025',
                startDate: '',
                endDate: '',
                voivodeship: '',
                email: '',
                errors: [],
                letters:[]
            }
        },
        methods: {
            checkForm: function (e) {
                this.errors = [];

                if (!this.validEmail(this.email)) {
                    this.errors.push('Niepoprawna domena (com.pl).');
                }
                if (moment(this.startDate).isAfter(this.endDate)) {
                    this.errors.push('Data końcowa musi być późniejsza od początkowej');
                }
                if (moment(this.startDate).isBefore(this.minDate) || moment(this.endDate).isBefore(this.minDate)) {
                    this.errors.push('Najwcześniesza dopuszczalna data to 01-01-1950');
                }
                if (moment(this.startDate).isAfter(this.maxDate) || moment(this.endDate).isAfter(this.maxDate)) {
                    this.errors.push('Najpóźniesza dopuszczalna data to 31-12-2025');
                }
console.log(this.errors);
            },
            startProcessing() {
//                e.preventDefault();
                this.checkForm();
                
                console.log('processing');
                console.log(this.errors);
                if (this.errors.length ==0) {
                    console.log('no errors - l 83');
                } else {
                    console.log('errors - l 85');
                }
            },
            validEmail: function (email) {
                if (/.com.pl\s*$/.test(email))
                {
                    return true;
                }
                return false;
            }
        }
    }

</script>
