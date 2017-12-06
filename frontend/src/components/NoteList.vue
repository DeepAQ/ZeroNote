<template>
  <div>
    <div class="search_bar">
      <NoteSearch v-on:nb-selected="nbSelected">
        <el-button slot="append" icon="el-icon-plus" v-on:click="newNbClick"/>
      </NoteSearch>
    </div>
    <el-menu ref="menu" v-loading="loading" v-on:open="menuNbOpen" :router="true">
      <el-submenu v-for="nb in notebooks" :key="nb.id" :index="nb.id + ''">
        <div slot="title" class="menu_item">
          <div>
            <i class="el-icon-edit-outline"></i>
            <span>{{nb.id == 0 ? 'Default notebook' : nb.name}}</span>
          </div>
          <div>
            <el-tooltip content="Delete notebook" placement="bottom-start">
              <i class="el-icon-delete" v-if="nb.id > 0" @click.stop="deleteNotebook(nb.id)"></i>
            </el-tooltip>
            <el-tooltip content="New note" placement="bottom-start">
              <i class="el-icon-plus" @click.stop="newNote(nb.id)"></i>
            </el-tooltip>
          </div>
        </div>
        <div v-loading="note_loading[nb.id]" style="min-height: 40px;">
          <el-menu-item v-for="nt in notes[nb.id]" :key="nt.id" :index="'/note/' + nb.id + '/' + nt.id">
            <div class="menu_item">
              <div>
                <i class="el-icon-document"></i>
                <span>{{(!nt.title || nt.title == '') ? 'Untitled' : nt.title}}</span>
              </div>
              <div>
                <el-tooltip content="Delete note" placement="bottom-start">
                  <i class="el-icon-delete" @click.stop="deleteNote(nb.id, nt.id)"></i>
                </el-tooltip>
              </div>
            </div>
          </el-menu-item>
          <div class="nodata" style="line-height: 40px;" v-if="!note_loading[nb.id] && (!notes[nb.id] || notes[nb.id].length == 0)">
            <i class="el-icon-document" style="font-size: 24px;"></i>
            <span style="font-size: 14px;">No notes in notebook</span>
          </div>
        </div>
      </el-submenu>
    </el-menu>
    <div class="nodata" v-if="!loading && notebooks.length == 0">
      <i class="el-icon-edit-outline" style="font-size: 60px;"></i><br><br>
      No notebooks,<br>try to create one~
    </div>
  </div>
</template>

<script>
import api from '../utils/api'
import NoteSearch from './NoteSearch'

export default {
  components: { NoteSearch },
  data () {
    return {
      loading: false,
      notebooks: [],
      note_loading: {},
      notes: {}
    }
  },
  computed: {
    routeNbid () {
      return this.$route.params.nbid
    },
    routeNtid() {
      return this.$route.params.id
    }
  },
  watch: {
    routeNtid () {
      this.routeChanged()
    }
  },
  methods: {
    routeChanged () {
      if (this.routeNbid >= 0) {
        this.nbSelected(this.routeNbid)
      }
      if (this.routeNtid > 0) {
        this.$refs.menu.activeIndex = `/note/${this.routeNbid}/${this.routeNtid}`
      }
    },
    nbSelected (nbid) {
      if (this.$refs.menu.openedMenus.indexOf(nbid) < 0) {
        this.$refs.menu.open(nbid)
        this.loadSubList(nbid)
      }
    },
    loadList () {
      this.loading = true
      api('notebook/all', {}).then(data => {
        this.notebooks = data
        setTimeout(this.routeChanged, 0)
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => {
        this.loading = false
      })
    },
    loadSubList (nbid) {
      this.$set(this.note_loading, nbid, true)
      api('note/get', {
        nbid: nbid
      }).then(data => {
        this.$set(this.notes, nbid, data)
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => {
        this.$set(this.note_loading, nbid, false)
      })
    },
    newNbClick () {
      this.$prompt('Name your new notebook', 'New notebook', {
        confirmButtonText: 'New',
        cancelButtonText: 'Cancel',
        inputPattern: /[^\s]+/,
        inputErrorMessage: 'Please type a notebook name'
      }).then(({ value }) => {
        api('notebook/create', {
          name: value
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: 'Notebook created'
          })
          this.loadList()
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: reason
          })
        })
      })
    },
    menuNbOpen (index, path) {
      this.loadSubList(index)
    },
    newNote (nbid) {
      api('note/create', {
        nbid: nbid
      }).then(data => {
        this.$message({
          showClose: true,
          type: 'success',
          message: 'Note created'
        })
        this.$router.push(`/note/${nbid}/${data}`)
        this.loadSubList(nbid)
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      })
    },
    deleteNotebook (nbid) {
      this.$confirm('Notebook will be deleted, are you sure? All notes will be moved to default notebook.', 'Confirm delete', {
        type: 'warning',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
      }).then(() => {
        api('notebook/delete', {
          nbid: nbid
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: 'Notebook deleted'
          })
          this.$router.push('/')
          this.loadList()
          this.loadSubList(0)
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: reason
          })
        })
      })
    },
    deleteNote (nbid, ntid) {
      this.$confirm('Deleted notes cannot be recovered, are you sure?', 'Confirm delete', {
        type: 'warning',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
      }).then(() => {
        api('note/delete', {
          id: ntid
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: 'Note deleted'
          })
          this.$router.push('/')
          this.loadSubList(nbid)
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: reason
          })
        })
      })
    }
  },
  mounted () {
    this.loadList()
  }
}
</script>

<style lang="less" scoped>
.search_bar {
  padding: 0 20px 10px 20px;
  display: flex;

  .note-search {
    flex: 1;
  }
}

.menu_item {
  display: flex;
  padding-right: 18px;

  >:first-child {
    flex: 1;
  }

  >:nth-child(2) i {
    font-size: 16px;
    width: 20px;
    margin: 0;
  }
}

.el-menu {
  min-height: 60px;
}

.el-menu, .el-submenu>:nth-child(2) {
  background: transparent;
}

.el-menu-item, .el-submenu>:first-child {
  height: 30px;
  line-height: 30px;
}

.el-menu-item {
  padding-right: 0;
}

.nodata {
  text-align: center;
}
</style>
