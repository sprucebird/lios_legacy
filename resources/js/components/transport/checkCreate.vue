<template>
<div>
  <div class="page-header mb-1">
    <div class="description">
      <h3>Ataskaitos</h3>
      <h1>Nauja transporto priem. bukles ivertinimo ataskaita</h1>
    </div>
    <br />

  </div>

  <div class="page-header mb-5">
    <button class="btn btn-danger" @click="cancel()" v-if="cont">Atšaukti ir grįžti atgal</button>
  </div>
  <div class="page-content justify-content-center mt-4">
    <div class="alert alert-danger" v-if="operationStatus == 'FAILED'">
      <div>Operacija atšaukta dėl sistemos klaidos</div>
    </div>
    <div class="alert alert-danger" v-if="operationStatus == 'FILES'">
      <div>Operacija atšaukta dėl failu ikelimo klaidos</div>
    </div>
    <div class="alert alert-success" v-if="operationStatus == 'SUCCESS'">
      <div>Ataskaita sukurta! Pridėkite nuotraukų ir spauskite "Pridėti"</div>
    </div>
    <div class="row">
      <div class="card big mt-5 col-md-3">
        <div class="card-header">

        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <h1>1</h1>
            </div>
            <div class="col-md-10">
              <p>
                Pasirinkite datą, kada buvo gautos nuotraukos bei transporto priemonės valstybinius numerius.
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">
              <h1>2</h1>
            </div>
            <div class="col-md-10">
              <p>
                Įveskite vardą bei pavardę asmens iš kurio buvo gautos nuotraukos bei kita medija.
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">
              <h1>3</h1>
            </div>
            <div class="col-md-10">
              <p>
                Užpildžius visą šią informaciją, atsiras failų įkėlimo laukas, į kurį galite įkelti nuotraukas. Maksimalus failo dydis: 500 MB
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="card big mt-5 col-md-8">
        <div class="card-header">
          Pridėti naują transporto priem. ataskaita
        </div>
        <div class="card-body">
          <div class="row">

            <div class="col-md-12">
              <div class="form-group">
                <label for="manifactuer">Ataskaitos data</label>
                <label class="tag tag-grey">* laukelis būtinas</label>
                <date-picker v-model="created_at" :config="options"></date-picker>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="VAN">Transporto priem. </label>
                <label class="tag tag-grey">* laukelis būtinas</label>
                <br />
                <label class="tag tag-red" v-if="transport.length == 0">Šią dieną visų Jūsų transporto priem. būklės ataskaitos yra parengtos.</label>
                <select class="form-control" v-model="VAT" @change="checkMinReq()" v-if="transport.length != 0">
                  <option v-bind:value="tr.VAT" v-for="tr in transport">{{tr.VAT}}</option>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="model">Nuotraukos ir kt. media priimta is</label>
                <label class="tag tag-grey">* laukelis būtinas</label>
                <input type="text" class="form-control" v-model="driver" v-on:keyup="checkMinReq()">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="model">Pastabos</label>
                <textarea type="text" class="form-control" v-model="comments" placeholder="" @change="checkMinReq()"></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <vue-dropzone v-if="minTrue" @vdropzone-queue-complete="dropComplete" @vdropzone-removed-file="vremoved" @vdropzone-sending="sendingEvent" ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
            </div>

          </div>
          <div class="row">
            <div class="col-md-6">
              <button class="btn btn-primary" @click="complete()" v-if="cont">Prideti</button>
            </div>
            <div class="col-md-6">

            </div>
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
  data() {
    return {
      operationStatus: 'on',
      transport: [],
      VAT: null,
      created_at: null,
      driver: null,
      comments: null,
      reportID: -1,
      files: null,
      minTrue: false,
      CalledFirst: false,
      cont: true,
      options: {
        format: 'YYYY-MM-DD',
        useCurrent: true,
      },
      dropzoneOptions: {
        url: '/api/media/upload/',
        headers: {
          'X-CSRF-TOKEN': document.head.querySelector("[name=csrf-token]").content
        },
        thumbnailWidth: 150,
        maxFilesize: 20,
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='fa fa-cloud-upload'></i>FAILUS TEMPKITE ČIA<br/>Išsaugojus ataskaitą failų nebus galima ištrinti!",
      },
    }
  },
  methods: {
    transportStore: function() {
      if (this.CalledFirst && this.reportID == -1) return;
      if (this.CalledFirst == false) this.CalledFirst = true;
      axios.post('/api/reports/transport/check/store', {
        VAT: this.VAT,
        created_at: this.created_at,
        comments: this.comments,
        driver: this.driver,
        id: this.reportID
      }).then(response => {
        this.reportID = response.data.id;
        if (response.data.status == "VALIDATION") {
          this.operationStatus = 'VALIDATION';
        } else if (response.data.status == "OK") {
          console.log("OK");
          this.operationStatus = 'SUCCESS';
        } else if (response.data.status == "FILES") {
          this.operationStatus = 'FILES';
        } else {
          console.log(response.data.status);
          this.operationStatus = 'FAILED';
        }
      });
    },

    complete() {
      axios.get('/vapi/reload');
      this.$router.push('/reports/transport/status');
      this.CalledFirst = false;
    },

    cancel() {
      if (this.reportID == -1) {
        this.$router.push("/reports/transport/status");
        return;
      }
      axios.post("/api/reports/transport/check/delete", {
        'id': this.reportID
      }).then(response => {
        this.$router.push("/reports/transport/status");
      });

    },

    sendingEvent(file, xhr, formData) {
      formData.append('reportID', this.reportID);
      formData.append('uuid', file.upload.uuid);
      this.cont = false;
    },

    dropComplete() {
      this.cont = true;
    },

    vremoved(file, xhr, error) {
      if (!this.CalledFirst) return;
      axios.post('/api/media/delete', {
        "id": this.reportID,
        "file": file.upload
      }).then(response => {
        if (response.data.status == "OK") console.log("OK");
        else console.log("Error");
      });
    },

    checkMinReq() {
      if (this.created_at != null && this.created_at != "" && this.VAT != null && this.VAT != "" && this.driver != null && this.driver != "") {
        this.transportStore();
        this.minTrue = true;
      } else this.minTrue = false;
    },

    handler() {
      if (!this.CalledFirst && this.reportID != -1) {
        axios.post("/api/reports/transport/check/delete", {
          'id': this.reportID
        }).then(response => {
          this.$router.push("/reports/transport/status");
        });
      }
    }

  },
  mounted() {
    this.created_at = (typeof this.$route.params.date != "undefined" ? this.$route.params.date : new Date());
    console.log(this.$route.params.date);
    axios.post('/api/transport/all/except', {
      'created_at': this.created_at
    }).then(response => {
      this.transport = response.data.transport;
    });
  },
  watch: {
    reportID: function(newVal, oldVal) {
      this.checkMinReq();
    },
    created_at: function(newVal, oldVal) {
      axios.post('/api/transport/all/except', {
        'created_at': newVal
      }).then(response => {
        this.transport = response.data.transport;
        this.VAT = null;
        this.checkMinReq();
      });
    }
  }
}
</script>

<style>
</style>
