<template>
    <div>
        <div v-show="displayFirstStep">
            <h3 class="text-center m-4">Wybierz wojwództwo, podaj email w domenie com.pl oraz wybierz datę początkową i końcową.</h3>
            <form @submit.prevent="startProcessing()">
                <p v-if="errors.length" class="text-danger">
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
                    <input type="date" id="start-date-input" class="form-control" v-model="startDate" required="">
                </div>
                <div class="form-group">
                    <label for="end-date-input">Data końcowa</label>
                    <input type="date" id="end-date-input" class="form-control" v-model="endDate" required="">
                </div>
                <div class="form-group">
                    <button type="submit">Dalej</button>
                </div>
            </form>
        </div>

        <div v-show="displaySecondStep" class="mt-5">
            <h3>Wybierz 10 liter</h3>
            <p>Ilość wybranych liter: {{ lettersNumber }}</p>
            <p v-show="countLettersError" class="text-danger">Proszę wybrać dokładnie 10 liter</p>
            <form @submit.prevent="generateUUID()">
                <div class="form-check" id="letters-list">
                    <span v-for="(letter,i) in letterList" class="col-2 ml-1">
                        <input type="checkbox" class="form-check-input" v-model="checkedLetters[i]" :value="letter"> 
                        <label class="form-check-label" for="">{{ letter }}</label>
                    </span>
                </div>

            </form>
            <hr/>
            <button @click="generateUUID()" class="btn btn-primary mb-3">Generuj UUID</button>
            <p v-show="noUuidError" class="text-danger">Proszę wygenerować lub podać kod UUID</p>
            <form @submit.prevent="generateStrings()" class="mt-1">
                <div class="form-group">
                    <input v-model="uuid" class="form-control">
                    <button class="btn btn-dark mt-5">Dalej</button>
                </div>
            </form>
        </div>

        <div v-show="displayThirdStep">
            <h3 class="mb-3">Podsumowanie:</h3>
            <p>Data początkowa: {{ startDate }}</p>
            <p>Data końocowa: {{ endDate }}</p>
            <p>Wybrane litery: {{ allLetters }}</p>
            <p>UUID: {{ uuid }}</p>

            <h5>Tekst wygenerowany dla pierwszego dnia zakresu</h5>
            <p>{{ firstGeneratedText }}</p>
            <button @click="sendToApi()" class="btn btn-info mt-5">Zapisz dane w API</button>
        </div>

    </div>
</div>
</template>

<script>
    import { uuid } from 'vue-uuid';

    export default {
        data() {
            return{
                minDate: '01-01-1950',
                maxDate: '31-12-2025',
                startDate: '',
                endDate: '',
                voivodeship: '',
                email: '',
                errors: [],
                letterList: [],
                selectedLetters: {},
                allLetters: '',
                checkedLetters: [],
                displayFirstStep: true,
                displaySecondStep: false,
                displayThirdStep: false,
                uuid: '',
                countLettersError: false,
                noUuidError: false,
                generatedText: '',
                firstGeneratedText: '',
                dateKey: '',
                charsReplace: {'ą': 'a', 'ę': 'e', 'ś': 's', 'ł': 'l', 'ż': 'z', 'ź': 'x', 'ć': 'c', '-': '', 'ó': 'o',
                    'Ą': 'A', 'Ę': 'E', 'Ś': 'S', 'Ł': 'L', 'Ż': 'Z', 'Ź': 'X', 'Ć': 'C', 'Ó': 'O'}
            }
        },
        methods: {
            checkForm: function () {
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
            },
            startProcessing() {
                this.checkForm();
                if (this.errors.length == 0) {
                    this.displayFirstStep = false;
                    this.displaySecondStep = true;
                    this.displayLetters();
                }
            },
            displayLetters() {
                let emailOnlyLetters = this.email.replace(/[^a-zA-ZĄąĆćĘęŁłŃńÓóŚśŹźŻż]/g, "").split('');
                this.letterList = this.shuffle(emailOnlyLetters.concat(this.voivodeship.replace(/[^a-zA-ZĄąĆćĘęŁłŃńÓóŚśŹźŻż]/g, "").split('')));
            },
            generateUUID() {
                this.uuid = uuid.v1();
            },
            validEmail: function (email) {
                if (/.com.pl\s*$/.test(email))
                {
                    return true;
                }
                return false;
            },
            generateStrings() {
                this.countLettersError = false;
                this.noUuidError = false;
                if (this.lettersNumber != 10) {
                    this.countLettersError = true;
                } else if (!this.uuid) {
                    this.noUuidError = true;
                } else {
                    this.displaySecondStep = false;
                    this.displayThirdStep = true;
                    let letters = [];
                    $('#letters-list input:checked').each(function () {
                        letters.push($(this).val());
                    });
                    letters.join('').replace(/[ĄąĆćĘęŻżŹźŁłÓóŃń-]/g, m => this.charsReplace[m]).split('');
                    this.allLetters = letters.join('');
                    let uuidArr = this.uuid.replace(/[ĄąĆćĘęŻżŹźŁłÓóŃń-]/g, m => this.charsReplace[m]).split(''),
                            uuidLen = uuidArr.length,
                            lettersLen = letters.length;
                    let maxLength = Math.max(lettersLen, uuidLen);
                    let uuidKey = 0;
                    let lettersKey = 0;

                    for (let i = 0; i < maxLength; i++) {
                        if (uuidKey >= uuidLen) {
                            uuidKey = 0;
                        }
                        if (lettersKey >= lettersLen) {
                            lettersKey = 0;
                        }
                        this.generatedText += uuidArr[uuidKey] + letters[lettersKey];
                        uuidKey++;
                        lettersKey++;
                    }
                    let MM = this.addLeadingZero(moment(this.startDate).get('month') + 1);
                    let DD = this.addLeadingZero(moment(this.startDate).get('date'));
                    let year = moment(this.startDate).get('year').toString();
                    let YY = this.addLeadingZero(year.substring(2, 4));
                    let XX = this.addLeadingZero(year.substr(0, 2));
                    this.firstGeneratedText = MM + XX + this.generatedText + DD + YY;
                }

            },
            shuffle(array) {
                array.sort(() => Math.random() - 0.5);
                return array;
            },
            addLeadingZero(number) {
                if (number < 10) {
                    return '0' + number;
                }
                return number.toString();
            },
            sendToApi() {
                fetch('api/key', {
                    method: 'post',
                    body: JSON.stringify({
                        start: this.startDate,
                        end: this.endDate,
                        text: this.generatedText
                    }),
                    headers: {
                        'content-type': 'application/json'
                    }
                }).then(res => res.json())
                        .then(data => {
                            alert('Dane zapisano');
                        })
                        .catch(err => console.log(err));
                ;
            }
        },
        computed: {
            lettersNumber() {
                this.selectedLetters = {};
                return this.checkedLetters.reduce(function (a, b) {
                    if (b) {
                        b = 1;
                    } else {
                        b = 0;
                    }
                    return parseInt(a) + parseInt(b);
                }, 0);
            }
        }
    }

</script>
