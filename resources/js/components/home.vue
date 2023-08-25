<template>
  <div>
    <div class="page-header mb-1">
      <div class="description">
        <h3>Sveiki!</h3>
        <h1>Pagrindinis</h1>
      </div>
    </div>
    <div class="page-content justify-content-center mt-4">
      <div class="row">
        <div class="col-md-3 card justify-content-center">
          <div
            class="card-sub-title mb-2 mt-2"
            style="text-align: center;"
          >Transporto priemonių skaičius</div>
          <div class="stat mt-4" style="text-align: center;">
            <h1 class="number">{{TrCount}}</h1>
          </div>
        </div>
        <div class="col-md-3 card justify-content-center">
          <div
            class="card-sub-title mb-2 mt-2"
            style="text-align: center;"
          >Sukurtų ataskaitų skaičius</div>
          <div class="stat mt-4" style="text-align: center;">
            <h1 class="number">{{AtCount}}</h1>
          </div>
        </div>
        <div class="col-md-3 card justify-content-center">
          <div class="card-sub-title mb-2 mt-2" style="text-align: center;">Viber įrenginių skaičius</div>
          <div class="stat mt-4" style="text-align: center;">
            <h1 class="number">{{ViberCount}}</h1>
          </div>
        </div>

        <div class="col-md-3 card justify-content-center">
          <div class="card-sub-title mb-2 mt-2" style="text-align: center;">Įkeltų nuotraukų kiekis</div>
          <div class="stat mt-4" style="text-align: center;">
            <h1 class="number">{{MediaCount}}</h1>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-12 card" style="height: 100vh;">
          <div class="card-header flex-inline">
            <span>Automobilių būkles ataskaitų dažnumas</span>
            <div class="dropdown">
              <button
                class="btn dropdown-toggle"
                type="button"
                id="dropdownMenuButton"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
              >{{currentTransportTypeInTransportStatusReports}}</button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" @click="changeTrType(2)">Puspriekabės</a>
                <a class="dropdown-item" href="#" @click="changeTrType(1)">Vilkikai</a>
                <a class="dropdown-item" href="#" @click="changeTrType(3)">Autovėžiai</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <apexchart type="heatmap" :options="options" :series="series"></apexchart>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      API_results: [],
      TrCount: null,
      AtCount: null,
      ViberCount: null,
      MediaCount: null,
      currentTransportTypeInTransportStatusReports: null,

      options: {
        chart: {
          id: "heatmap1"
        },
        xaxis: {
          categories: [
            "Sausis",
            "Vasaris",
            "Kovas",
            "Balandis",
            "Geguze",
            "Birzelis",
            "Liepa",
            "Rugpjutis",
            "Rugsejis",
            "Spalis",
            "Lapkritis",
            "Gruodis"
          ]
        }
      },
      series: []
    };
  },
  methods: {
    changeTrType(type) {
      if (type == 1) {
        axios.post("api/stats/reports/", { type: 1 }).then(response => {
          this.series = response.data.Transport;
          this.currentTransportTypeInTransportStatusReports = "Vilkikai";
          this.MediaCount = response.data.media;
        });
      } else {
        axios.post("api/stats/reports/", { type: 2 }).then(response => {
          this.series = response.data.Transport;
          this.currentTransportTypeInTransportStatusReports = "Puspriekabės";
          this.MediaCount = response.data.media;
        });
      }
    }
  },
  mounted() {
    this.$parent.contentLoadingStart();
    axios.get("api/stats/transport").then(response => {
      this.TrCount = response.data.count;
    });
    axios.post("api/stats/reports/", { type: 1 }).then(response => {
      this.AtCount = response.data.count;
      this.series = response.data.Transport;
      this.ViberCount = response.data.Viber;
      this.MediaCount = response.data.media;
      this.currentTransportTypeInTransportStatusReports = "Vilkikai";
      this.$parent.contentLoadingStop();
    });
  }
};
</script>

<style>
</style>
