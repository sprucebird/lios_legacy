<template>
<div>
  <div class="page-header mb-4">
      <div class="description">
        <h3>Vartotojai</h3>
        <h1>Sukurti nauja vartotoja</h1>
      </div>
      <div class="ml-5 stats">
         <div class="actions">
          <router-link to="/users" class="btn btn-danger">
            <span>Atšaukti</span>
          </router-link>
        </div>
      </div>
    </div>

    <div class="page-content justify-content-center">
    <div class="card card-big">
       <div class="alert alert-success" v-if="operationStatus">
        <h2 style="text-transform: uppercase;color: #fff;font-weight: bolder;">Registracijos kodas buvo sėkmingai sugeneruotas!</h2>
        <h4>Nusiųskite jį būsimam vartotojui. Jis galės prisiregistruoti prie sistemos adresu: <a href="https://lios.sprucebird.com/signup">lios.sprucebird.com/signup</a></h4>
      </div>
      <div class="alert alert-danger" v-if="serverError">
        <h2 style="text-transform: uppercase;color: #fff;font-weight: bolder;">Serverio klaida</h2>
        <h4>Pabandykite atlikti šį veiksmą po keletos minučių ir jei tai nepadeda susisiekite su techninio aptarnavimo komanda</h4>
        <h5 style="color: #fff;">Klaidos tekstas: {{serverErrorMessage}} </h5>
      </div>
      <div class="card-body">
        <div class="col-md-12">
                          <div class="form-row">
                            <div class="form-group col-md-12">
                                <h2>Čia bus sugeneruotas kodas, kuris leis naujo sistemos vartotojo registraciją. Jį nusiųskite būsimam vartotojui.</h2>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="text" class="form-control input-search" style="font-size: 1.8em; border: none;font-weight: bolder; padding: 2rem .7rem;padding-left:0;letter-spacing: 10px;background: none !important;" placeholder="Naujas kodas atsiras cia" v-model="code" disabled>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary" v-on:click="userCreate" v-if="operationStatus == false">Generuoti kodą naujo vartotojo registracijai</a>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script>

  export default {
    data(){
      return{
        operationStatus: false,
        serverError: null,
        serverErrorMessage: null,
        validationFailed: null,
        alreadyExcists: false,
        code: null,
      }
    },
    mounted(){
    },
    methods: {
      userCreate: function(){
        axios.get('/api/users/new/generate', {

        }).then(response => {
          if (response.data.status == 'OK')
          {
            this.operationStatus = true;
            this.code = response.data.code;
          }else{
            if(response.data.cause == 1){
              this.validationFailed = true;
            }else{
              this.serverError = true;
              this.serverErrorMessage = response.data;
            }

            if(response.data.cause == 3){this.alreadyExcists = true}
            console.log(response.data);
          }
        });
      }
    }
  }
</script>

<style>
</style>
