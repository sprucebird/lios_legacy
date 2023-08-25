<template>
  <div>
  <div class="page-header mb-1">
      <div class="description">
        <h3>Nustatymai</h3>
        <h1>Sistemos vartotojai</h1>
      </div>

  </div>
  <div class="page-content justify-content-center mt-4">
    <div class="card big mt-5">
      <div class="card-header">
        Visi sistemos vartotojai
        <router-link to="/settings/users/create" class="tag tag-blue ml-4">
            Naujas vartotojas
        </router-link>
      </div>
      <div class="card-body">
       <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                      <thead>
                        <tr role="row">
                          <th class="w-1 sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="No.: activate to sort column descending" style="width: 45px;">Unik. Nr.</th>
                          <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Invoice Subject: activate to sort column ascending" style="width: 171px;">Vardas, pavardė</th>
                          <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Client: activate to sort column ascending" style="width: 130px;">El. paštas</th>
                          <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="VAT No.: activate to sort column ascending" style="width: 81px;">Pareigos</th>
                          <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="VAT No.: activate to sort column ascending" style="width: 81px;">Sukurtas</th>
                          <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 146px;">Būsena</th>
                        </tr>
                      </thead>
                      <tbody>
                      <tr role="row" class="odd" v-for="result in API_results">
                          <td class="sorting_1"><span class="text-muted">{{result.id}}</span></td>
                          <td><a href="invoice.html" class="text-inherit">{{result.name}}</a></td>
                          <td><a href="invoice.html" class="text-inherit">{{result.email}}</a></td>
                          <td>
                            <span class="bg-label bg-label-danger">Nenustatyta</span>
                          </td>
                          <td>
                            {{result.created_at}}
                          </td>
                          <td>
                            <span v-if="result.email_verified_at == null"><span class="status-icon bg-warning" ></span>Nepatvirtintas El. pašto adresas</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
        </div>
        <!-- <div class="alert alert-primary">
          Atsiprašome, šiuo metu veiksmų centras yra tik kūrimo stadijoje
        </div> -->
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
      }
    },
    mounted() {
      axios.get('/api/users').then(response => {
        console.log(response.data);
        this.API_results = response.data;
      });

    }
  }
</script>

<style>
</style>