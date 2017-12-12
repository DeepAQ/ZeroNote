<template>
  <div>
    <el-menu ref="menu" v-loading="loading" :router="true">
      <el-menu-item v-for="nt in notes" :key="nt.id" :index="'/share/' + nt.id">
        <div class="title">
          <i class="el-icon-document"></i>
          {{(!nt.title || nt.title == '') ? 'Untitled' : nt.title}}
        </div>
        <div class="user">{{ nt.user.nickname }} &lt;{{ nt.user.email }}&gt;</div>
        <div class="time">{{ (new Date(nt.created * 1000)).toLocaleString() }}</div>
      </el-menu-item>
    </el-menu>
    <div class="nodata" v-if="!loading && notes.length == 0">
      <i class="el-icon-share" style="font-size: 60px;"></i><br><br>
      No notebooks shared to me
    </div>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      loading: false,
      notes: []
    }
  },
  computed: {
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
      if (this.routeNtid > 0) {
        this.$refs.menu.activeIndex = `/share/${this.routeNtid}`
      }
    },
    loadList () {
      this.loading = true
      api('share/tome', {}).then(data => {
        this.notes = data
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
    }
  },
  mounted () {
    this.loadList()
  }
}
</script>

<style lang="less" scoped>
.el-menu {
  min-height: 60px;
  background: transparent;

  .el-menu-item {
    min-height: 70px;
    padding-right: 0;

    .title {
      line-height: 30px;
    }

    .user, .time {
      margin-left: 32px;
      line-height: 16px;
      font-size: 12px;
      color: #666;
    }
  }
}

.nodata {
  text-align: center;
}
</style>
