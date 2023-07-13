<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Transportas</h3>
        <h1>Nauja transporto priemonė</h1>
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
              <label for="VAN">Valstybiniai transporto priemonės numeriai</label>
              <input type="text" class="form-control" style="text-transform: uppercase;letter-spacing: 5px;" v-model="VAT" placeholder="Pvz: ABC111" @keyup="detectTransportType">
            </div>
          </div>
           <div class="col-md-4">
            <div class="form-group">
              <label for="VAN">Tipas</label>
              <select class="form-control" v-model="category">
                <option value="1">Lengvasis automobilis</option>
                <option value="2" selected>Vilkikas</option>
                <option value="3">Puspriekabė</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="manifactuer">Gamintojas</label>
              <input type="text" class="form-control" v-model="manifactuer" placeholder="Pvz: Iveco">
            </div>
          </div>
           <div class="col-md-4">
            <div class="form-group">
              <label for="model">Modelis</label>
              <input type="text" class="form-control" v-model="model" placeholder="">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="model">Metai</label>
              <input type="text" class="form-control" data-mask="0000" data-mask-clearifnotmatch="true" placeholder="0000" autocomplete="off" maxlength="4" v-model="rlYear">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-1">
            <button class="btn btn-primary" @click="transportStore">Prideti</button>
          </div>
          <div class="col-md-1">
            <button class="btn btn-danger" @click="$router.push('/transport')">Atsaukti</button>
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
        rlYear: null,
        category: null,
  		}
  	},
    methods: {
      detectTransportType()
      {
        if(this.VAT.length == 6)
        {
          this.category = 2
        }else{
          this.category = 3
        }
      },
      transportStore: function(){
          axios.post('/api/transport/store',
          {
            VAT: this.VAT,
            manufacturer: this.manifactuer,
            model: this.model,
            rlYear: this.rlYear,
            category: this.category,
          }
          ).then(response => {
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
    }
  }
</script>

<style>
</style>