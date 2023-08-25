<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Sveiki sugrįžę!</h3>
        <h1>Pagrindinis</h1>
      </div>
  </div>
  <div class="page-header mb-5">
      </div>
  <div class="page-content justify-content-center mt-4">
    <div class="card big mt-5">
      <div class="card-header">
        Veiksmų centras
      </div>
      <div class="card-body">
       <!--  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                      <thead>
                        <tr role="row"><th class="w-1 sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="No.: activate to sort column descending" style="width: 45px;">Unik. Nr.</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Invoice Subject: activate to sort column ascending" style="width: 171px;">Veiksmas</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Client: activate to sort column ascending" style="width: 130px;">Narys</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="VAT No.: activate to sort column ascending" style="width: 81px;">Veiksma atliko</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Created: activate to sort column ascending" style="width: 103px;">Atliktas</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 146px;">Busena</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label=": activate to sort column ascending" style="width: 175px;"></th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label=": activate to sort column ascending" style="width: 29px;"></th></tr>
                      </thead>
                      <tbody>
                      <tr role="row" class="odd">
                          <td class="sorting_1"><span class="text-muted">001401</span></td>
                          <td><a href="invoice.html" class="text-inherit">Atliktas naujas mokejimas</a></td>
                          <td>
                            Juozas Skarbalius
                          </td>
                          <td>
                            Karolis Paradnikas
                          </td>
                          <td>
                            2019-06-27 18:55:34
                          </td>
                          <td>
                            <span class="status-icon bg-success"></span> Paid
                          </td>
                        </tr>
                      </tbody>
                    </table>
        </div> -->
        <div class="alert alert-primary">
          Atsiprašome, šiuo metu veiksmų centras yra tik kūrimo stadijoje
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
  			API_results: [],
        signupsCount: null,
        signupsCount_date: null,
        income: null,
        income_date: null,
        outcome: 0,
        outcome_date: null,
        membersCount: null,
        membersCount_date: null,
  		}
  	},
    mounted() {
      console.log('mounted');

      axios.get('api/stats/signups/1').then(response => {
        this.signupsCount = response.data.count;
        this.signupsCount_date = response.data.date;
      });
      axios.get('api/stats/members/count').then(response => {
        this.membersCount = response.data.count;
      });
      axios.get('api/stats/economy/income/1').then(response => {
        this.income = response.data.income;
        this.income_date = response.data.date;
      });
    }
  }
</script>

<style>
</style>