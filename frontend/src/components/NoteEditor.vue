<template>
  <div id="editor" v-loading="loading">
    <div id="title_bar">
      <el-input v-model="title" placeholder="Untitled" v-on:blur="saveTitle">
        <span slot="prepend">Title</span>
        <span slot="append">
          <span v-if="saving"><i class="el-icon-loading"></i> Syncing</span>
          <span v-else><i class="el-icon-check"></i> Latest</span>
        </span>
      </el-input>
    </div>
    <mavon-editor v-model="content" language="en"/>
  </div>
</template>

<script>
import api from '../utils/api'
import _ from 'lodash'

export default {
  props: ['nbid', 'id'],
  data () {
    return {
      loading: false,
      saving: false,
      title: '',
      content: ''
    }
  },
  mounted () {
    this.loadNote()
  },
  watch: {
    id () {
      this.loadNote()
    },
    content () {
      if (!this.loading) {
        this.saveContent()
      }
    }
  },
  methods: {
    loadNote () {
      this.loading = true
      api('note/single', {
        id: this.id
      }).then(data => {
        this.title = data.title ? data.title : ''
        this.content = data.content ? data.content : ''
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    saveTitle () {
      if (!this.saving) {
        this.saving = true
        api('note/updatetitle', {
          id: this.id,
          title: this.title
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: 'Title changed'
          })
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: `Autosave failed: ${reason}`
          })
        }).then(() => this.saving = false)
      }
    },
    saveContent: _.debounce(function () {
      if (!this.saving) {
        this.saving = true
        api('note/updatecontent', {
          id: this.id,
          content: this.content
        }).then(data => {}).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: `Autosave failed: ${reason}`
          })
        }).then(() => this.saving = false)
      }
    }, 2000)
  }
}
</script>

<style lang="less" scoped>
#editor {
  height: 100%;
  display: flex;
  flex-direction: column;

  #title_bar {
    padding-bottom: 10px;
  }

  .v-note-wrapper {
    flex: 1;
  }
}
</style>
