  <template>
  <div>
	<div class="page-header page-content mb-1" style="justify-content: space-between;">
      <div class="description">
        <h3>Ataskaitos</h3>
        <h1>Transporto priemonių būklė</h1>
      </div>
      <div class="col-md-4">
               <div class="selectgroup w-100">
                              <label class="selectgroup-item" @click="changeView(2)">
                                <input type="radio" name="transportation" value="2" class="selectgroup-input" :checked="viewMode=='calendar'">
                                <span class="selectgroup-button selectgroup-button-icon">
                                  <i class="fe fe-calendar icon mr-2"></i>
                                  Kalendorius
                                </span>
                              </label>
                              <label class="selectgroup-item" @click="changeView(1)">
                                <input type="radio" name="transportation" value="1" class="selectgroup-input" :checked="viewMode=='table'">
                                <span class="selectgroup-button selectgroup-button-icon">
                                  <i class="icon fe fe-filter mr-2"></i>
                                  Lentelė
                                </span>
                              </label>
                </div>
      </div>
  </div>
  <div class="page-content justify-content-center mt-4">
    <div class="page-content justify-content-center mt-4" v-if="viewMode == 'calendar'">
      <div class="row mb-3">
        <div class="col-md-6">
          <button type="button" class="btn btn-pill btn-secondary mr-3" @click="calendarBack()">
            <i class="fe fe-arrow-left mr-2 icon"></i>
          </button>
          <button type="button" class="btn btn-pill btn-secondary mr-4" @click="calendarNext()">
            <i class="fe fe-arrow-right ml-2 icon"></i>
          </button>
          <span>{{monthName}}</span>
        </div>
      </div>
      <calendar
        style="height: 100vh;width: 100%;"
        ref="Tuicalendar"
        view="month"
        :calendars="calendarList"
        :schedules="reportList"
        :useCreationPopup="false"
        :useDetailPopup="true"
        :disableDblClick="false"
        :month="monthSettings"
        @beforeCreateSchedule="dblclick"
        @beforeUpdateSchedule="update"
        @beforeDeleteSchedule="deleteSchedule"
       />
    </div>


    <div class="row mt-5 mb-5" v-if="viewMode == 'table'">
      <div class="col-md-2 f">
        <label for="" class="label">VAT</label>
        <select class="form-control form-control-filter" v-model="VAT_">
          <option value="0">Visi automobiliai</option>
          <option v-for="auto in double" :value="auto.VAT">{{auto.VAT}}</option>
        </select>
      </div>
       <div class="col-md-2 f">
        <label class="label">Data</label>
        <date-picker class="form-control-filter" v-model="date_" :config="options"></date-picker>
      </div>

      <div class="col-md-2 f">
        <label class="label"></label>
        <button class="form-control btn mt-2 form-control-filter" @click="() => {this.date_=null; this.VAT_=0;}">
          Išvalyti filtrus
        </button>
      </div>
    </div>
     <div class="alert alert-warning" v-if="notFound">
          <div>Nieko nerasta</div>
      </div>
    <div class="card big mt-5" v-if="viewMode == 'table'">
      <div class="card-header flex-inline">
        <div>Visos ataskaitos</div>

        <div>
          <input type="checkbox" v-model="scrollStatus" @click="scroll(true)"> Rodyti naujausius</input>

          </div>

         <router-link to="/reports/transport/status/create" class="btn btn-small btn-primary" data-toggle="tooltip" title="Main">
            <i class="fe fe-plus"></i>
            Nauja ataskaita
          </router-link>
      </div>
      <div class="card-body">
        <radar-spinner
          v-if="isLoading"
          :animation-duration="2000"
          :size="60"
          color="#4062BB"
        />
        <table class="table card-table table-striped table-vcenter">
                      <thead>
                        <tr>
                          <th>Transporto priem.</th>
                          <th>Vairuotojas</th>
                          <th>Būsena</th>
                          <th>Nuotraukos/media</th>
                          <th>Pastabos</th>
                          <th>Sukurta</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(result, index) in API_results.reports.data" :id="result.id">
                          <td>
                            <div v-if="result.status == 0">
                              <a href="javascript:void(0)" data-toggle="dropdown" v-if="result.VAT">
                                <span class="tag tag-grey">{{result.VAT}}</span>
                              </a>

                              <div class="dropdown-menu dropdown-menu-left mt-3">
                                <a v-for="auto in double" @click="changeVAT(auto.VAT, result.id, result.created_at, result.driver, result.comments)" class="dropdown-item">{{auto.VAT}}</a>
                              </div>

                            </div>
                            <div v-if="result.status == 2">
                              <span class="bg-label bg-label-warning" v-if="result.VAT == '######'">Laukiama</span>
                              <span class="tag tag-grey" v-if="result.VAT != '######'">{{result.VAT}}</span>
                            </div>
                            <div v-if="result.status == 1">
                              <span class="tag tag-grey">{{result.VAT}}</span>
                            </div>
                          </td>
                          <td>
                            {{result.driver}}
                          </td>
                          <td>
                            <span class="bg-label bg-label-success" v-if="result.status == 0 || result.status == 1">Patikrinta</span>
                            <!-- <span class="bg-label bg-label-warning" v-if="result.status == 1">Nepateikta VAT</span> -->
                            <span class="bg-label bg-label-warning" v-if="result.status == 2">Pildoma</span>
                          </td>
                          <td class="text-nowrap">
                            <div class="row" v-for="media in result.media">

                              <img class="col-md-2" style="max-height: 70px" v-if="media.src != null" @click="showPhotoSwipe(0, index)" :src="media.thumb">
                            </div>
                            <!-- <button type="button" 
                                    v-if="result.media != null" 
                                    class="dropdown-item" 
                                    name="button" 
                                    @click="showPhotoSwipe(0, index)">
                                    <i v-if="result.id < 295 || result.id == 313" class="dropdown-icon fe fe-play"></i> 
                                    <img 
                                        v-if="result.id >= 295 && result.id != 313" 
                                        :src="result.thumb" 
                                        
                                        style="max-height: 70px;"> </button> -->
                            <v-photoswipe v-if="result.media != null" :isOpen="isOpen[index]" :items="result.media" :options="opt" @close="hidePhotoSwipe(index)"></v-photoswipe>
                            <span class="tag tag-red" v-if="result.media == null">Nera</span>

                          </td>
                          <td>
                            <span class="bg-label bg-label-primary" v-if="result.comments != null">Yra</span>
                          </td>
                          <!-- <td>
                            {{result.driver}}
                          </td> -->
                          <td>
                            {{result.created_at}}
                          </td>
                          <td>
                            <div class="item-action dropdown">
                              <a href="javascript:void(0)" data-toggle="dropdown" class="icon icon-table"><i class="fe fe-more-vertical"></i></a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a @click="edit(result)" class="dropdown-item"><i class="dropdown-icon fe fe-edit"></i> Redaguoti </a>
                                <a class="dropdown-item" v-if="result.media!=null"  @click="download(result.id, result.created_at, result.VAT)"> <i class="dropdown-icon fe fe-download"</i> Parsisiųsti mediją </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" @click="rem(result.id)" v-if="result.status != 2"><i class="dropdown-icon fe fe-trash"></i> Pašalinti</a>
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
</template>

<script>
import moment from 'moment';

import {
  PhotoSwipe,
  PhotoSwipeGallery
} from 'v-photoswipe';

export default {

  components: {
    'v-photoswipe': PhotoSwipe,
    'v-photoswipe-gallery': PhotoSwipeGallery
  },
  data() {
    return {
      viewMode: 'calendar',
      isLoading: true,
      API_results: {reports: []},
      date_: null,
      double: [],
      VAT_: 0,
      reportList: [],
      currentDateRangeStart: null,
      currentDateRangeEnd: null,
      monthName: null,
      notFound: false,
      scrollStatus: true,
      isOpen: [],
      opt: {
        index: 0
      },
      calendarList: [{
        id: 1,
        name: 'Transporto bukles ivertinimai'
      }],
      options: {
        format: 'YYYY-MM-DD',

        useCurrent: true,
      },
      monthSettings: {
        daynames: ['Sek', 'Pir', 'Ant', 'Treč', 'Ket', 'Penk', 'Šeš'],
        startDayOfWeek: 1,
        narrowWeekend: false
      },
      scheduleList: [],
    }
  },

  methods: {
    scroll(check) {
      if (check && this.scrollStatus) return;
      window.scrollTo(0, 0);
    },

    listenForChanges() {
      var chan = Echo.channel('update');
      chan.listen('.reload', (e) => {
        if(this.viewMode == 'calendar') this.mount();
        else this.updateTable();
        if (this.scrollStatus) this.scroll(false);
      });
    },
    showPhotoSwipe(index, row) {
      this.$set(this.opt, 'index', index);
      this.$set(this.isOpen, row, true);
    },
    hidePhotoSwipe(index) {
      this.$set(this.isOpen, index, false);
    },
    rem(id) {
      let aId = this.API_results.reports.data.findIndex(x => x.id == id);
      this.API_results.reports.data.splice(aId, 1);
      axios.post("/api/reports/transport/check/delete", {
        'id': id,
        'save': true,
      }).then(response => {
        // let el = document.getElementById(id);
        // el.parentNode.removeChild(el);
        axios.get('/vapi/reload');
      });
    },

    edit(rep) {
      this.$router.push({
        name: 'update',
        params: rep
      });
    },

    filter(VAT, date) {
      if(this.viewMode != 'table' || this.API_results == []) return;
      axios.post('/api/check/filter', {
        VAT: VAT,
        date: date
      }).then(response => {
        //CORRECTED
        this.API_results.reports.data = response.data;
      });
    },
    changeView(mode) {
      if (mode == 1) {
        this.viewMode = 'table';
        this.updateTable();
      } else {
        this.viewMode = 'calendar';
        this.mount();
      }
    },
    calendarNext() {
      this.$refs.Tuicalendar.invoke('next');
      this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
      this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
      this.monthName = moment(this.$refs.Tuicalendar.invoke('getDate')._date, 'MMMM, YYYY').locale('lt').format('MMMM, YYYY').toUpperCase();
      //this.currentDate = moment(this.currentDate).add(1, 'month');
    },
    calendarBack() {
      this.$refs.Tuicalendar.invoke('prev');
      this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
      this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
      this.monthName = moment(this.$refs.Tuicalendar.invoke('getDate')._date, 'MMMM, YYYY').locale('lt').format('MMMM, YYYY').toUpperCase();
      //this.currentDate.moment().add(1, 'months');
    },
    download(id, created_at, VAT) {
      axios({
        url: '/api/media/download',
        method: 'POST',
        params: {
          id: id
        },
        responseType: 'blob', // important
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', VAT + '---' + created_at + '.zip'); //or any other extension
        document.body.appendChild(link);
        link.click();
      });
    },

    dblclick(res) {
      var parse = moment(res.start._date, 'YYYY-MM-DD').format("YYYY-MM-DD");
      this.$router.push({
        name: 'create',
        params: {
          date: parse
        }
      });
    },

    update(res) {
      var VAT = res.schedule.title;
      var date = moment(res.schedule.start._date, 'YYYY-MM-DD').format("YYYY-MM-DD");
      axios.post('/api/calendarFind/false', {
        VAT: VAT,
        date: date
      }).then(response => {
        this.edit(response.data);
      });
    },

    deleteSchedule(res) {
      var VAT = res.schedule.title;
      var date = moment(res.schedule.start._date, 'YYYY-MM-DD').format("YYYY-MM-DD");
      axios.post('/api/calendarFind/true', {
        VAT: VAT,
        date: date
      }).then(response => {
        this.isLoading = true;
        if(this.viewMode == 'calendar') this.mount();
        else this.updateTable();
        this.isLoading = false;
      });
    },

    changeVAT(e, id, c, d, cm) {
      axios.post('/api/reports/transport/check/update', {
        reportID: id,
        VAT: e,
        created_at: c,
        driver: d,
        comment: cm
      }).then(response => {
        axios.get('/vapi/reload');
      });
    },

    mount(checker) {
      if(this.viewMode == "calendar") 
         axios.get('/api/reports/transport/check/calendar').then(response => {
          this.reportList = response.data.calendar;
      });
      else 
        axios.get('/api/reports/transport/check/table').then(response => {
         if (checker != null && checker) {
           this.API_results = response.data;
         }
         else this.filter(this.VAT_, this.date_);
          if (response.data.reports.length == 0)
           this.notFound = true;
          else this.notFound = false;
          this.API_results.timestamp = new Date();
        });


      axios.get('/api/transport/all').then(response => {
        this.double = response.data.transport;
        this.isLoading = false;
      });
    },
    updateTable() {
      if(!this.API_results.timestamp) {
        this.mount(true);
        return;
      }
      axios.post('/api/reports/transport/check/table/update', {
        timestamp: this.API_results.timestamp
      }).then(response => {
          for(let upd of response.data.updated) {
            let curr = this.API_results.reports.data.findIndex(x => x.id == upd.id);
            if(curr >= 0) this.API_results.reports.data[curr] = upd;
            else this.API_results.reports.data.unshift(upd);
          }
          this.$forceUpdate();
      });
        this.API_results.timestamp = new Date();
    }
  },

  computed: {

  },
  async mounted() {
    this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
    this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
    this.monthName = moment(this.$refs.Tuicalendar.invoke('getDate')._date, 'MMMM, YYYY').locale('lt').format('MMMM, YYYY').toUpperCase();
    if (this.$cookies.isKey("viewMode")) {
      this.viewMode = this.$cookies.get("viewMode");
    } else {
      this.$cookies.set("ViewMode", "table");
    }
    await this.mount(true);
    this.listenForChanges();
  },
  watch: {
    VAT_: function(newVal, oldVal) {
      this.filter(newVal, this.date_);
    },
    date_: function(newVal, oldVal) {
      this.filter(this.VAT_, newVal);
    },
    viewMode: function(newVal, oldVal) {
      this.$cookies.set("viewMode", newVal);
    }

  }
}
</script>

<style>
.dropdown-item {
  cursor: pointer;
}

html {
  scroll-behavior: smooth;
}
</style>
