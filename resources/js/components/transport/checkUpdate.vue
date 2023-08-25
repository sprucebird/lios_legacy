<template>
  <div>
  <div class="page-header mb-1">
      <div class="description">
        <h3>Transporto priem. bukles ivertinimo ataskaita</h3>
        <h1>{{VAT}} {{created_at}}</h1>
      </div>
  </div>
  <div class="page-header mb-5">
      </div>
  <div class="page-content justify-content-center mt-4">
    <div class="alert alert-danger" v-if="operationStatus == 'FAILED'">
          <div>Operacija atšaukta dėl sistemos klaidos</div>
        </div>
        <div class="alert alert-danger" v-if="operationStatus == 'FILES'">
              <div>Operacija atšaukta dėl failu ikelimo klaidos</div>
            </div>
        <div class="alert alert-success" v-if="operationStatus == 'SUCCESS'">
          <div>Operacija atilkta!</div>
        </div>
    <div class="card big mt-5">
      <div class="card-header">
        Transporto priem. būklės ataskaita
      </div>
      <div class="card-body">
        <div class="row">
           <div class="col-md-4">
            <div class="form-group">
              <label for="VAN">Transporto priem. </label>
              <select class="form-control" :value="VAT" disabled>
                <option :value="VAT">{{VAT}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label for="manifactuer">Ataskaitos data</label>
              <date-picker v-model="created_at" :config="options" disabled></date-picker>
            </div>
          </div>
           <div class="col-md-12">
            <div class="form-group">
              <label for="model">Pastabos</label>
              <textarea type="text" class="form-control" v-model="comments" placeholder=""></textarea>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="model">Nuotraukos ir kt. media priimta is</label>
              <input type="text" class="form-control" v-model="driver">
            </div>
          </div>
          <div class="col-md-8">
              <label for="media">Nuotraukos</label>
              <div>



                <v-photoswipe-gallery :isOpen="isOpenGallery" :options="optionsGallery" :items="items">
                  <img style="max-height: 70px;" class="ml-2" slot-scope="props" :src="props.item.src" alt="picture"/>
                </v-photoswipe-gallery>




              </div>
            </div>
            <v-photoswipe :isOpen="isOpen" :items="items" :options="LBoptions" @close="hidePhotoSwipe"></v-photoswipe>
          </div>
          <div class="col-md-12">
            <div class="alert alert-primary" v-if="files_enabled == false">Užpildykite privalomus laukelius (Valstybiniai numeriai, ataskaitos data, pranešėjas), kad galėtumėte įkelti failus</div>
            <label>Prideti papildomus failus</label>
            <vue-dropzone @vdropzone-sending="sendingEvent" @vuedropzone-removed-file="vremoved" ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" v-if="files_enabled == true"></vue-dropzone>
          </div>

        </div>
        <div class="row">
          <div class="col-md-12 mt-3 mb-4" v-if="files_enabled == true">
            <div class="col-md-6">
              <button class="btn btn-primary" @click="finishOperation">Patvirtinti</button>
              <button class="btn btn-danger" @click="back()">Atsaukti</button>
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
  import { PhotoSwipe, PhotoSwipeGallery } from 'v-photoswipe'
  export default {
    components: {
      'v-photoswipe': PhotoSwipe,
      'v-photoswipe-gallery': PhotoSwipeGallery
    },
    data(){
      return{
        operationStatus: 'on',
        transport: [],
        VAT: this.$route.params.VAT,
        created_at: this.$route.params.created_at,
        driver: this.$route.params.driver,
        comments: this.$route.params.comments,
        reportID: this.$route.params.id,
        CalledFirst: true,
        items: [],
        images: {},
        files_enabled: true,
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
          dictDefaultMessage: "<i class='fa fa-cloud-upload'></i>FAILUS TEMPKITE ČIA",
        },
        isOpen: false,
        isOpenGallery: false,
        LBoptions: {
          index: 0
        },
        optionsGallery: {

        },
        }
    },
    methods: {
      finishOperation: function(){
          axios.post('/api/reports/transport/check/update', {
              comments: this.comments,
              driver: this.driver,
              reportID: this.reportID,
              VAT: this.VAT,
          }).then(response =>{
            if(response.data.status == "OK"){
              axios.get('/vapi/reload');
              this.CalledFirst = false;
              swal("Duomenys atnaujinti", 'Operacija atilkta sėkmingai', 'success');
              setTimeout(() => {swal.close(); this.$router.back()}, 1000);
            }
            else operationStatus = "FAILED";
          });
        },

        back() {
          this.$router.back();
        },

      sendingEvent (file, xhr, formData) {
        formData.append('reportID', this.reportID);
        formData.append('uuid', file.upload.uuid);
      },
      vremoved(file, xhr, error) {
        if(!this.CalledFirst) return;
        axios.post('/api/media/delete', {
          "id": this.reportID,
          "file": file.upload
        }).then(response => {
          if(response.data.status == "OK") console.log("OK");
          else console.log("Error");
        });
      },
      showPhotoSwipe (index) {
        this.isOpen = true
        this.$set(this.options, 'index', index)
      },
      hidePhotoSwipe () {
        this.isOpen = false
      }
  },
  mounted() {
      axios.post('/api/media/thumbs', {id: this.reportID}).then(response => {

        response.data.forEach((item) => {
          item.src = item.src
          item.w = item.w
          item.h = item.h
          this.items.push(item)
        })
      });
  },
  watch: {
    driver: function(newVal, oldVal) {
      this.files_enabled = (newVal != "" && newVal != " " && newVal != null ? true : false);
    }
  }

}
</script>

<style>
</style>
