<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Vairuotojai</h3>
        <h1>Prideti naujo vairuotojo duomenis</h1>
      </div>
  </div>
  <div class="page-header mb-5">
      </div>
  <div class="page-content justify-content-center mt-4">
    <div class="alert alert-danger" v-if="operationStatus == 'FAILED'">
          <div>Operacija atšaukta dėl sistemos klaidos</div>
        </div>
        <div class="alert alert-success" v-if="operationStatus == 'SUCCESS'">
          <div>Operacija atilkta!</div>
        </div>
    <div class="card big mt-5">
      <div class="card-header">
        Pridėti naują transporto priemonę
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label for="VAN">Vardas, pavarde</label>
              <input type="text" class="form-control" style="text-transform: uppercase;letter-spacing: 5px;" v-model="name" placeholder="Verdenis Pavardenis">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="phone">Telefono numeris</label>
              <input type="text" class="form-control" v-model="phone" placeholder="+370XXXXXXXX">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-1">
            <button class="btn btn-primary" @click="storeD">Prideti</button>
          </div>
          <div class="col-md-1">
            <button class="btn btn-danger" @click="$router.push('/drivers')">Atsaukti</button>
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
  		}
  	},
    methods: {
      storeD: function(){
          axios.post('/api/drivers/store',
          {
            name: this.name,
            phone: this.phone,
          }
          ).then(response => {
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
    }
  }
</script>

<style>
</style>
