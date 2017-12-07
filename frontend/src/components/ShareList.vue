<template>
  <div>
    <el-menu ref="menu" v-loading="loading" :router="true">
      <el-menu-item v-for="nt in notes" :key="nt.id" :index="'/share/' + nt.id">
        <i class="el-icon-document"></i>
        <span>{{(!nt.title || nt.title == '') ? 'Untitled' : nt.title}}</span>
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
    height: 30px;
    line-height: 30px;
    padding-right: 0;
  }
}

.nodata {
  text-align: center;
}
</style>
