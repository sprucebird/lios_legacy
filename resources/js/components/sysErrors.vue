<template>
<div>
  <div class="page-header mb-1">
    <div class="description">
      <h3>Sistema</h3>
      <h1>Sistemos klaidos</h1>
    </div>

  </div>
  <div class="page-content justify-content-center mt-4 flex-inline">

    <div class="table-responsive">
      <table class="table card-table table-vcenter text-nowrap datatable dataTable no-footer overflow-hidden" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
        <thead>
          <tr role="row" class="row">
            <th class="col-1">Eil. nr.</th>
            <th class="col-4">Vairuotojas</th>
            <th class="col-4">Klaida</th>
            <th class="col-3">Veiksmai</th>
          </tr>
        </thead>
        <tbody>
          <tr role="row" class="odd row" v-for="(result, index) in API_results">
            <td class="col-1">{{index+1}}</td>
            <td class="col-3">{{result.name}}</td>
            <td class="col-4 danger" alt="Vairuotojo valstybiniai numeriai nepriskirti, tačiau ataskaita pildoma.">
              C-001 (######)
            </td>
            <td class="col-3">
              <button class="btn btn-warning" @click="fix(result.id)">Taisyti</button>
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
</template>

<script>
export default {
  data() {
    return {
      API_results: [],
    }
  },
  methods: {
    fix(id) {
      axios.post('/api/system/errors', {
        "err": id
      }).then(response => {
        this.mount();
      });
    },
    mount() {
      axios.get('/api/system/errors').then(response => {
        this.API_results = response.data;
      });
    }
  },
  mounted() {
    this.mount();
  }
}
</script>

<style scoped>
.danger {
  background-color: #c81414;
  color: #ffffff;
}

.btn {
  height: 1.5rem !important;
  display: -webkit-flex;
  display: -ms-flex;
  display: flex;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
  justify-content: center;
}
</style>
