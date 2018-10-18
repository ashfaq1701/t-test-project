<template>
  <v-container>
    <v-layout row wrap justify-center>
      <v-flex xs12>
        <img class="profile-img-view" :src="profilePicture"/>
      </v-flex>
      <v-flex xs12>
        <v-btn @click="pickFile" color="primary">Upload New</v-btn>
        <input
          type="file"
          style="display: none"
          ref="profileImage"
          accept="image/*"
          @change="onFilePicked"
        >
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import Vue from 'vue'
  import { uploadPhoto } from '../../api/photos'

  export default Vue.component('profile_image_uploader', {
    data () {
      return {
        uploadedProfilePhoto: ''
      }
    },
    methods: {
      pickFile: function () {
        this.$refs.profileImage.click()
      },
      onFilePicked: function (e) {
        const files = e.target.files
        let self = this
        self.$emit('photo_upload_started')
        if (files[0] !== undefined) {
          this.imageName = files[0].name
          if (this.imageName.lastIndexOf('.') <= 0) {
            return
          }
          const fr = new FileReader()
          fr.readAsDataURL(files[0])
          fr.addEventListener('load', () => {
            this.uploadedProfilePhoto = fr.result
            let imageFile = files[0]
            uploadPhoto(imageFile).then(function (response) {
              self.$emit('uploaded_profile_photo', response.data.data.id)
            }).catch(function (error) {
              self.uploadedProfilePhoto = ''
              self.$emit('upload_failed_profile_photo', error)
            })
          })
        } else {
          this.uploadedProfilePhoto = ''
          this.$emit('upload_profile_photo_cancelled')
        }
      }
    },
    computed: {
      isAuthenticated () {
        return this.$store.getters.isAuthenticated
      },
      user () {
        return this.$store.getters.currentUser
      },
      profilePicture () {
        if (this.uploadedProfilePhoto !== '') {
          return this.uploadedProfilePhoto
        }
        let fileName = 'user.png'
        if (this.user !== null && this.user.profile_photo !== null) {
          fileName = this.user.profile_photo.file_name
        }
        return '/images/' + fileName
      }
    }
  })
</script>

<style>
  .profile-img-view {
    height: 150px;
    width: 150px;
    border-radius: 50%;
  }
</style>
