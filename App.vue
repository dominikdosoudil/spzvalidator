<template>
    <div>
        <select v-model="type">
            <option v-for="(type, index) in types" v-bind:value="index">{{ type.name | capitalize }}</option>
        </select>
        <input type="text" v-model="spz">
        <div>{{ spz | uppercase }}</div>
        <p v-if="error & errorEnum.UNMATCHED_LENGTH">Znaků musí být {{ types[type].maxLength }}</p>
        <p v-if="error & errorEnum.WRONG_CHARS">SPZ může obsahovat pouze čísla a znaky abecedy bez diakritiky kromě O, Q, W a G.</p>
        <p v-if="error & errorEnum.NO_NUM">SPZ musí obsahovat alespoň jedno číslo</p>
        <p v-if="error & errorEnum.ALREADY_USED">Tato SPZ je již registrována</p>
        <p v-if="error & errorEnum.API_OFFLINE">Omlouváme se, ale služba pro kontrolu duplicity a registraci SPZ je momentálně offline.<p>
        <button v-if="error === 0 | error === 16" v-on:click="apiPost">Registrovat</button>
    </div>
</template>
<style>
    body{
        background-color:#ff0000;
    }
</style>
<script>
    export default{
        data(){
            return{
                spz:'',
                type: 1,
                error: 0,
                types: {
                    1: {name: 'auto', maxLength: 8},
                    2: {name: 'motocykl', maxLength: 7},
                    3: {name: 'moped', maxLength: 5}
                },
                errorEnum: {
                    NO_NUM: 1,
                    UNMATCHED_LENGTH: 2,
                    WRONG_CHARS: 4,
                    ALREADY_USED: 8,
                    API_OFFLINE: 16,
                }
            }
        },
        watch: {
            spz: function(spz) {
                this.validator(spz);
                this.apiGet(spz);
            }
        },
        mounted(){
            this.validator(this.spz);
        },
        methods: {
            addError(error){
                if((this.error & this.errorEnum[error]) === 0) {
                    this.error += this.errorEnum[error];
                }
            },
            removeError(error){
                if((this.error & this.errorEnum[error]) > 0) {
                    this.error -= this.errorEnum[error];
                }
            },
            validator(spz){
                if(spz.length != this.types[this.type].maxLength) {
                    this.addError('UNMATCHED_LENGTH');
                } else {
                    this.removeError('UNMATCHED_LENGTH');
                }

                if(/([^\w]|_|[QOWG])/gi.test(spz)){
                    this.addError('WRONG_CHARS');
                } else {
                    this.removeError('WRONG_CHARS');
                }

                if(!/\w*[\d]+\w*/.test(spz)){
                    this.addError('NO_NUM');
                } else {
                    this.removeError('NO_NUM');
                }
            },
            apiGet(spz){
                console.log('Calling the API.');
                this.$http.get('/api.php', {params: {spz: spz}}).then((response) => {
                    this.removeError('API_OFFLINE');
                    if(response.body > 0) {
                        this.addError('ALREADY_USED');
                    } else {
                        this.removeError('ALREADY_USED');
                    }
                }, (response) => {
                    this.addError('API_OFFLINE');
                });
            },
            apiPost(){
                this.spz = this.spz.toUpperCase();
                this.$http.post('/api.php',{spz: this.spz}).then((response) => {
                    this.removeError('API_OFFLINE');
                    console.log(response);
                    if(response.body == 1) {
                        alert('Registrováno.');
                        this.apiGet(this.spz);
                    }
                }, (response) => {
                    console.log(response);
                    this.addError('API_OFFLINE');
                });
            }
        },
        filters: {
            capitalize: function (value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1)
            },
            uppercase: function (val) {
                if (!val) return '';
                val = val.toString();
                return val.toUpperCase();
            }
        },
        http: {
            emulateJSON: true,
            emulateHTTP: true
        },
    }
</script>
