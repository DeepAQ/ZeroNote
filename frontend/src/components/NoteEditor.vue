<template>
  <div id="editor" v-loading="loading">
    <div id="title_bar">
      <div>
        <el-input v-model="title" placeholder="Untitled" v-on:blur="saveTitle" :disabled="permission < 2">
          <span slot="prepend">Title</span>
          <span slot="append">
            <span v-if="saving"><i class="el-icon-loading"></i> Syncing</span>
            <span v-else><i class="el-icon-check"></i> Latest</span>
          </span>
        </el-input>
      </div>
      <div>
        <el-button v-if="permission == 3" type="primary" icon="el-icon-share" v-on:click="shareNote">Share</el-button>
        <el-dropdown v-on:command="exportNote">
          <el-button type="primary" icon="el-icon-download">
            Export <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="markdown">Markdown</el-dropdown-item>
            <el-dropdown-item command="pdf">PDF</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
    <mavon-editor
      ref="editor"
      v-model="content"
      language="en"
      :editable="permission >= 2"
      v-on:save="saveContent"
      v-on:imgAdd="uploadFile"/>
    <NoteShare ref="share"/>
  </div>
</template>

<script>
import NoteShare from './NoteShare'
import api from '../utils/api'
import _ from 'lodash'
import DMP from 'diff-match-patch'

let serverContent = ''
let pollStarted = false
const dmp = new DMP()

export default {
  components: { NoteShare },
  props: ['id'],
  data () {
    return {
      loading: false,
      polling: false,
      saving: false,
      permission: 0,
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
      if (!this.loading && !this.polling) {
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
        serverContent = data.content ? data.content : ''
        this.content = serverContent
        this.title = data.title ? data.title : ''
        this.permission = data.permission
        if (!pollStarted) {
          pollStarted = true
          setTimeout(this.pollChanges, 1000)
        }
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
        const patch = dmp.patch_toText(dmp.patch_make(serverContent, this.content))
        if (patch == '') return
        this.saving = true
        api('note/patch', {
          id: this.id,
          patch: patch
        }).then(data => {
          serverContent = data.content
        }).catch(reason => {
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
    },
    shareNote () {
      this.$refs.share.showShare(this.id)
    },
    pollChanges () {
      this.polling = true
      api('note/poll', {
        id: this.id,
        content: serverContent
      }).then(data => {
        if (data.patch != '') {
          const patches = dmp.patch_fromText(data.patch)
          serverContent = dmp.patch_apply(patches, serverContent)[0]
          const newContent = dmp.patch_apply(patches, this.content)[0]
          const textArea = this.$el.querySelector('.auto-textarea-input')
          let cursorPos = textArea.selectionStart
          if (this.content.substr(0, cursorPos) != newContent.substr(0, cursorPos)) {
            cursorPos += newContent.length - this.content.length
          }
          textArea.disabled = true
          this.content = newContent
          setTimeout(() => {
            textArea.disabled = false
            textArea.focus()
            textArea.selectionStart = textArea.selectionEnd = cursorPos
          }, 0)
        }
      }).catch(reason => {}).then(() => {
        setTimeout(this.pollChanges, 1000)
        this.polling = false
      })
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
      margin-left: 5px;
    }
  }

  .v-note-wrapper {
    flex: 1;
  }
}
</style>
