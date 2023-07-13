<template>
  <div>
  <div class="page-header mb-1">
      <div class="description">
        <h3>vairuotojai</h3>
        <h1>{{name}}</h1>
      </div>
  </div>
  <div class="page-header mb-5">
    <button class="btn btn-danger" @click="$router.push('/drivers')">Atšaukti ir grįžti atgal</button>
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
              <label for="VAN">Vardas, pavarde</label>
              <input type="text" class="form-control" v-model="name" placeholder="Vardenis Pavardenis">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="manifactuer">Telefono numeris</label>
              <input type="text" class="form-control" v-model="phone" placeholder="+370XXXXXXXX">
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
        name: null,
        phone: null,
        id: null,
      }
    },
    methods: {
      update(){
        axios.post('/api/drivers/' + this.id + '/update',
        {
          name: this.name,
          phone: this.phone,
        }
        ).then(response => {
          console.log(response);
          if(response.data.status == "VALIDATION")
          {
            this.operationStatus = 'VALIDATION';
          }
          if(response.data.status == "OK"){
            this.operationStatus = 'SUCCESS';
            setTimeout(() => this.$router.push('/drivers'), 1200);
          }else{
            this.operationStatus = 'FAILED';
          }
        });
        }
    },
    mounted() {
      console.log('mounted');
      axios.get('/api/drivers/' + this.$route.params.id).then(response => {
        this.name = response.data.driver.name;
        this.phone = response.data.driver.phone;
        this.id = response.data.driver.id
      });

    }
  }
</script>

<style>
</style>
