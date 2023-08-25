<template>
  <div>
  <div class="page-header mb-1">
      <div class="description">
        <h3>Transportas</h3>
        <h1>{{VAT}}</h1>
      </div>
  </div>
  <div class="page-header mb-5">
    <button class="btn btn-danger" @click="$router.push('/transport')">Atšaukti ir grįžti atgal</button>
  </div>
  <div class="page-content justify-content-center mt-4">
    <div class="alert alert-danger" v-if="operationStatus == 'FAILED'">
      <div>Operacija atšaukta dėl sistemos klaidos</div>
    </div>
    <div class="alert alert-success" v-if="operationStatus == 'SUCCESS'">
      <div>Operacija atlikta sėkmingai</div>
      <div>Netrukus būsite nukreipti į ankstesnį puslapį</div>
    </div>
    <div class="card big mt-5">
      <div class="card-header">
        Redaguoti {{VAT}}

      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="VAN">Valstybiniai transporto priemonės numeriai</label>
              <input type="text" class="form-control" v-model="VAT" placeholder="Pvz: ABC111">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="manifactuer">Gamintojas</label>
              <input type="text" class="form-control" v-model="manifactuer" placeholder="Pvz: Iveco">
            </div>
          </div>
           <div class="col-md-12">
            <div class="form-group">
              <label for="model">Modelis</label>
              <input type="text" class="form-control" v-model="model" placeholder="">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <button class="btn btn-primary" @click="update">Atnaujinti duomenis</button>
          </div>
        </div>
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
        operationStatus: 'on',
        VAT: null,
        manifactuer: null,
        model: null,
      }
    },
    methods: {
      update(){
        axios.post('/api/transport/' + this.$route.params.id + '/update',
        {
          VAT: this.VAT,
          manufacturer: this.manifactuer,
          model: this.model,
        }
        ).then(response => {
          console.log(response);
          if(response.data.status == "VALIDATION")
          {
            this.operationStatus = 'VALIDATION';
          }
          if(response.data.status == "OK"){
            this.operationStatus = 'SUCCESS';
            setTimeout(() => this.$router.push('/transport'), 1200);
          }else{
            this.operationStatus = 'FAILED';
          }
        });
        }
    },
    mounted() {
      console.log('mounted');
      axios.get('/api/transport/' + this.$route.params.id).then(response => {
        this.VAT = response.data.transport.VAT;
        this.manifactuer = response.data.transport.manufacturer;
        this.model = response.data.transport.model;
      });

    }
  }
</script>

<style>
</style>