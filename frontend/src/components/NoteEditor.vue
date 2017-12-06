<template>
  <div id="editor" v-loading="loading">
    <div id="title_bar">
      <div>
        <el-input v-model="title" placeholder="Untitled" v-on:blur="saveTitle">
          <span slot="prepend">Title</span>
          <span slot="append">
            <span v-if="saving"><i class="el-icon-loading"></i> Syncing</span>
            <span v-else><i class="el-icon-check"></i> Latest</span>
          </span>
        </el-input>
      </div>
      <div>
        <el-dropdown v-on:command="exportNote">
          <el-button type="primary">
            <i class="el-icon-download"></i> Export <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="markdown">Markdown</el-dropdown-item>
            <el-dropdown-item command="pdf">PDF</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
    <mavon-editor ref="editor" v-model="content" language="en" v-on:save="saveContent" v-on:imgAdd="uploadFile"/>
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
        this.saveDebounce()
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
        }).then(data => {}).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: `Autosave failed: ${reason}`
          })
        }).then(() => this.saving = false)
      }
    },
    saveContent () {
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
    },
    saveDebounce: _.debounce(function () {
      this.saveContent()
    }, 1000),
    uploadFile (filename, file) {
      let formData = new FormData();
      formData.append('filename', filename)
      formData.append('file', file)
      api('attachment/upload', formData).then(data => {
        this.$refs.editor.$img2Url(filename, data)
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: `Upload failed: ${reason}`
        })
      })
    },
    exportNote (type) {
      switch (type) {
        case 'markdown':
          let a = document.createElement('a')
          a.href = URL.createObjectURL(new Blob([this.content]))
          a.download = (this.title == '' ? 'Untitled' : this.title) + '.md'
          a.click()
          URL.revokeObjectURL(a.href)
          break
        case 'pdf':
          let w = window.open()
          w.document.write(`<html><head><title>${this.title}</title></head><body>${this.$refs.editor.d_render}</body></html>`)
          w.print()
          w.close()
          break
      }
    }
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
    display: flex;

    >:first-child {
      flex: 1;
    }

    >:last-child {
      margin-left: 10px;
    }
  }

  .v-note-wrapper {
    flex: 1;
  }
}
</style>
