<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Transportas</h3>
        <h1>Visas transportas</h1>
      </div>
  </div>
  <div class="page-header mb-5">
      </div>
  <div class="page-content justify-content-center mt-4">
    <div class="card big mt-5">
      <div class="card-header flex-inline">
        <div>Visas transportas</div>
         <router-link to="/transport/create" class="btn btn-small btn-primary" data-toggle="tooltip" title="Main">
            <i class="fe fe-plus"></i> Prideti
          </router-link>
      </div>
      <div class="card-body">
        <div class="alert alert-warning" v-if="notFound">
          <div>Nieko nerasta</div>
        </div>
        <table class="table card-table table-striped table-vcenter">
                      <thead>
                        <tr>
                          <th>VAN</th>
                          <th>Tipas</th>
                          <th>Prideta</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="result in API_results">
                          <td>
                            <div>{{result.VAT}}</div>
                            <div class="small text-muted">{{result.manufacturer}} {{result.model}} {{result.rlYear}}</div>
                          </td>
                          <td class="">
                            <div class="text-muted" v-if="result.category == 1">Lengvasis automobilis</div>
                            <div class="text-muted" v-if="result.category == 2">Vilkikas</div>
                            <div class="text-muted" v-if="result.category == 3">Puspriekabė</div>
                          </td>
                          <td class="text-nowrap">{{result.created_at}}</td>
                          <td>
                            <div class="item-action dropdown">
                              <a href="javascript:void(0)" data-toggle="dropdown" class="icon icon-table"><i class="fe fe-more-vertical"></i></a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a @click="editDialog(result.id)" class="dropdown-item"><i class="dropdown-icon fe fe-edit"></i> Redaguoti </a>
                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-edit-2"></i> Būklės ataskaita </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-trash"></i> Pašalinti</a>
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
        this.$router.push('transport/edit/' + id);
      },
    //   deleteDialog(id)
    //   {
    //      Swal.fire({
    //         title: 'Ar tikrai norite pašalinti šią transporto priemonę iš sistemos?',
    //         text: "Norėdami pašalinti, patvirtinkite šį veiksmą paspausdami mygtuką: Pašalinti",
    //         type: 'warning',
    //         showCancelButton: true,
    //         cancelButtonText: 'Atšaukti',
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Pašalinti'
    //     }).then((result) => {
    //         if (result.value) {
    //           Swal.fire(
    //             'Transporto priemonė sėkmingai ištrinta iš sistemos!',
    //             'success'
    //           )
    //         }
    //     });
    //   }
    },
    mounted() {
      axios.get('/api/transport/all').then(response => {
        if(response.data.transport[0].id == null){
          this.notFound = true;
        }
        this.API_results = response.data.transport;
      });
    }
  }
</script>

<style>
</style>