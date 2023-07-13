<template>
  <div>
	<div class="page-header mb-1">
      <div class="description">
        <h3>Development testing</h3>
        <h1>Calendar</h1>
      </div>
  </div>



</div>
</template>

<script>
import VuePureLightBox from 'vue-pure-lightbox';
import styles from 'vue-pure-lightbox/dist/VuePureLightbox.css';
//Javascript date formatter
  //Name: Moment.js
  //Web: https://momentjs.com
  import moment from 'moment';
  //setting up Vue filter to format date for calendar month string

  export default {
    components: {
      VuePureLightBox
    },
  	data(){
  		return{
  			API_results: [],
        reportList: [],
        currentDateRangeStart: null,
        currentDateRangeEnd: null,
        calendarList: [
          {
            id: 1,
            name: 'Bukles ivertinimai'
          }
        ],
        options: {
          format: 'YYYY-MM-DD',

          useCurrent: true,
        },

        monthSettings:
          {
            daynames: ['Sek', 'Pir', 'Ant', 'Treč', 'Ket', 'Penk', 'Šeš'],
            startDayOfWeek: 0,
            narrowWeekend: true
          },
        scheduleList: [
                {
                    id: '1',
                    calendarId: '1',
                    title: 'my schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2019-07-18T22:30:00+09:00',
                    end: '2019-07-19T02:30:00+09:00'
                },
                {
                    id: '2',
                    calendarId: '1',
                    title: 'second schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2018-10-18T17:30:00+09:00',
                    end: '2018-10-19T17:31:00+09:00'
                }
            ],
  		}
  	},
    methods: {
        calendarNext() {
          this.$refs.Tuicalendar.invoke('next');
          this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          //this.currentDate = moment(this.currentDate).add(1, 'month');
        },
        calendarBack() {
          this.$refs.Tuicalendar.invoke('prev');
          this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          //this.currentDate.moment().add(1, 'months');
        },
        rem(id) {
          axios.post("/api/reports/transport/check/delete", {
            'id': id,
            'save': true
          }).then(response => {
              this.mount();
          });
        },

        download(id, created_at, VAT) {
          axios({
                  url: '/api/media/download',
                  method: 'POST',
                  params: {id: id},
                  responseType: 'blob', // important
                }).then((response) => {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                  link.setAttribute('download', VAT+'---'+created_at+'.zip'); //or any other extension
                  document.body.appendChild(link);
                  link.click();
                });
        },
        mount() {
          //calendar default settings
          this.currentDateRangeStart = moment(this.$refs.Tuicalendar.invoke('getDateRangeStart')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          this.currentDateRangeEnd = moment(this.$refs.Tuicalendar.invoke('getDateRangeEnd')._date, 'YYYY-MM-DD').format('YYYY-MM-DD');
          //calendar default settings END
          axios.get('/api/reports/transport/check').then(response => {
            console.log(response);
            this.reportList = response.data.calendar;
          });
        }
    },
    mounted() {
        this.mount();
    }
  }
</script>

<style>
.dropdown-item {
  cursor: pointer;
}
</style>
