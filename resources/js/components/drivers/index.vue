<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Vairuotojai</h3>
        <h1>Visi vairuotojai</h1>
      </div>
  </div>
  <div class="page-header mb-5">
      </div>
  <div class="page-content justify-content-center mt-4">
    <div class="card big mt-5">
      <div class="card-header flex-inline">
        <div>Visi vairuotojai</div>
         <!-- <router-link to="/drivers/create" class="btn btn-small btn-primary" data-toggle="tooltip" title="Main" disabled>
            <i class="fe fe-plus"></i> Naujas
          </router-link> -->
      </div>
      <div class="card-body">
        <div class="alert alert-warning" v-if="notFound">
          <div>Nieko nerasta</div>
        </div>
        <table class="table card-table table-striped table-vcenter">
                      <thead>
                        <tr>
                          <th>Vardas, pavarde</th>
                          <th>Transporto priemone</th>
                          <th>Telefono numeris</th>
                          <th>Viber kodas</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(result, index) in API_results" :class="{red : !result.verified}">
                          <td>
                            <div>{{result.name}}</div>
                          </td>
                          <td>
                            <span class="bg-label bg-label-warning" v-if="result.VAT == null">Nepriskirta</span>
                            <div v-if="result.VAT != null">{{result.VAT}}</div>
                          </td>
                          <td>
                            <div>{{result.phone}} </div>
                          </td>
                          <td>
                            <span class="" v-if="result.viberId == null">Nepriskirta</span>
                            <div v-if="result.viberId != null">{{result.viberId}}</div>
                          </td>
                          <td>
                            <div class="item-action dropdown">
                              <a href="javascript:void(0)" data-toggle="dropdown" class="icon icon-table"><i class="fe fe-more-vertical"></i></a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a v-if="result.verified" @click="editDialog(result.id)" class="dropdown-item"><i class="dropdown-icon fe fe-edit"></i> Redaguoti </a>
                                <a v-if="!result.verified" @click="verify(result, index)" class="dropdown-item"><i class="dropdown-icon fe fe-check"></i> Patvirtinti </a>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
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
  			API_results: [],
        notFound: false,
  		}
  	},
    methods: {
      editDialog(id)
      {
        this.$router.push('drivers/edit/' + id);
      },
      verify(driver, index) {
        axios.post('api/drivertable/verify', {
          name: driver.name, 
          viber: driver.viberId
        }).then(response => {
          if(response.data.status == "200") this.API_results[index].verified = true;
        });
      }
    },
    mounted() {
      axios.get('/api/drivers/all').then(response => {
        this.API_results = response.data.drivers;
        console.log(this.API_results);
      });
    }
  }
</script>

<style scoped> 
.red {
  background-color: #f34d4de0 !important;
}
</style>
