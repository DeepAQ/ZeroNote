<template>
  <div id="editor" v-loading="loading">
    <div id="title_bar">
      <div>
        <el-input v-model="title" placeholder="Untitled" v-on:blur="saveTitle" :disabled="permission < 2">
          <span slot="prepend">Title</span>
          <span slot="append">
            <span v-if="saving"><i class="el-icon-loading"></i> Syncing</span>
            <span v-else>
              <span v-if="permission < 2"><i class="el-icon-warning"></i> Read-only</span>
              <span v-else><i class="el-icon-check"></i> Latest</span>
            </span>
          </span>
        </el-input>
      </div>
      <div>
        <el-popover ref="poptags" placement="bottom-end" width="300px">
          <el-tag v-for="tag in tags" :key="tag" closable @close="removeTag(tag)">{{ tag }}</el-tag>
          <el-input
            v-if="tagInput"
            v-model="tagValue"
            ref="saveTagInput"
            size="small"
            @keyup.enter.native="newTag"
            @blur="newTag">
          </el-input>
          <el-button v-else class="button-new-tag" size="small" @click="showTagInput">+ New Tag</el-button>
        </el-popover>
        <el-button-group>
          <el-button v-popover:poptags type="primary" icon="el-icon-menu">
            Tags
          </el-button>
          <el-button type="primary" v-on:click="upDownVote">
            <i class="el-icon-star-on" v-if="upvoted"></i>
            <i class="el-icon-star-off" v-else></i>
            {{ upvotes }}
          </el-button>
          <el-button v-if="permission == 3" type="primary" icon="el-icon-share" v-on:click="shareNote">
            Share
          </el-button>
        </el-button-group>
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
      v-on:imgAdd="uploadFile"/>
    <NoteShare ref="share"/>
  </div>
</template>

<script>
import NoteShare from './NoteShare'
import api from '../utils/api'
import _ from 'lodash'
import DMP from 'diff-match-patch'

let pollState = 0
let pollInterval = 0
let cBase = ''
const dmp = new DMP()

export default {
  components: { NoteShare },
  props: ['nbid', 'id'],
  data () {
    return {
      loading: false,
      saving: false,
      permission: 0,
      title: '',
      content: '',
      upvotes: 0,
      upvoted: false,
      tags: [],
      tagInput: false,
      tagValue: ''
    }
  },
  mounted () {
    this.loadNote()
    this.pollChanges()
  },
  beforeDestroy () {
    pollState = 0
  },
  watch: {
    id () {
      this.loadNote()
    },
    content () {
      pollState = 2
    },
    tags () {
      if (!this.loading && !this.saving) {
        this.saving = true
        api('note/updatetags', {
          id: this.id,
          tags: JSON.stringify(this.tags)
        }).then(data => {}).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: `Tags save failed: ${reason}`
          })
        }).then(() => this.saving = false)
      }
    }
  },
  methods: {
    loadNote () {
      pollState = 0
      this.loading = true
      api('note/single', {
        id: this.id
      }).then(data => {
        cBase = data.content ? data.content : ''
        this.content = cBase
        this.title = data.title ? data.title : ''
        this.permission = data.permission
        this.upvotes = data.upvotes
        this.upvoted = data.upvoted
        this.tags = []
        try {
          const tags = JSON.parse(data.tags)
          if (tags && tags.length > 0) {
            this.tags = tags
          }
        } catch (e) {}
        if (pollState == 0) {
          pollState = 1
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
        }).then(() => {
          this.saving = false
          this.$emit('nbReload', this.nbid)
        })
      }
    },
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
          w.document.write(`<html><head><title>${this.title}</title><style>img{max-width:100%;}</style></head>
          <body>${this.$refs.editor.d_render}</body></html>`)
          setTimeout(() => {
            w.print()
            w.close()
          }, 0)
          break
      }
    },
    shareNote () {
      this.$refs.share.showShare(this.id)
    },
    pollChanges () {
      console.log(`pollState: ${pollState}`)
      switch (pollState) {
        case 0:
          setTimeout(this.pollChanges, 1000)
          break
        case 1:
          api('note/poll', {
            id: this.id,
            content: cBase
          }).then(data => {
            if (data.patch != '') {
              const patches = dmp.patch_fromText(data.patch)
              cBase = dmp.patch_apply(patches, cBase)[0]
              const newContent = dmp.patch_apply(patches, this.content)[0]
              const textArea = this.$el.querySelector('.auto-textarea-input')
              let cursorStart = textArea.selectionStart
              let cursorEnd = textArea.selectionEnd
              if (this.content.substr(0, cursorStart) != newContent.substr(0, cursorStart)) {
                cursorStart += newContent.length - this.content.length
              }
              if (this.content.substr(0, cursorEnd) != newContent.substr(0, cursorEnd)) {
                cursorEnd += newContent.length - this.content.length
              }
              // textArea.disabled = true
              this.content = newContent
              this.$nextTick(() => {
                // textArea.disabled = false
                // textArea.focus()
                textArea.selectionStart = cursorPos
                textArea.selectionEnd = cursorEnd
              })
            }
          }).catch(reason => {}).then(() => {
            setTimeout(this.pollChanges, 1000)
          })
          break
        case 2:
          const cSnapshot = this.content
          const patch = dmp.patch_toText(dmp.patch_make(cBase, cSnapshot))
          if (patch == '') {
            pollState = 1
            this.pollChanges()
            break
          }
          this.saving = true
          api('note/patch', {
            id: this.id,
            patch: patch
          }).then(data => {
            cBase = cSnapshot
          }).catch(reason => {
            this.$message({
              showClose: true,
              type: 'error',
              message: `Autosave failed: ${reason}`
            })
          }).then(() => {
            this.saving = false
            setTimeout(this.pollChanges, 1000)
          })
          break
      }
    },
    upDownVote () {
      api('note/updownvote', {
        noteid: this.id
      }).then(data => {
        this.upvotes = data.upvotes
        this.upvoted = data.upvoted
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: `Upload failed: ${reason}`
        })
      })
    },
    showTagInput () {
      this.tagInput = true
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus()
      })
    },
    newTag () {
      if (this.tagValue != '') {
        if (this.tags.indexOf(this.tagValue) < 0) {
          this.tags.push(this.tagValue)
        }
        this.tagValue = ''
      }
      this.tagInput = false
    },
    removeTag (tag) {
      this.tags.splice(this.tags.indexOf(tag), 1)
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
