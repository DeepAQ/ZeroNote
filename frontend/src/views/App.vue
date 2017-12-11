<template>
  <el-container id="app">
    <el-header height="50px">
      <div class="title">ZeroNote</div>
      <div class="nav"></div>
      <div class="user">
        <el-dropdown v-on:command="logoutClick">
          <span class="el-dropdown-link">
            {{nickname}} &lt;{{email}}&gt; <i class="el-icon-arrow-down el-icon--right"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item>Logout</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </el-header>
    <el-container>
      <el-aside>
        <el-tabs :value="tab">
          <el-tab-pane label="Notes" name="note">
            <NoteList/>
          </el-tab-pane>
          <el-tab-pane label="Shared" name="share">
            <ShareList/>
          </el-tab-pane>
          <el-tab-pane label="Timeline" name="timeline">
            <Timeline/>
          </el-tab-pane>
        </el-tabs>
      </el-aside>
      <el-main>
        <router-view/>
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import NoteList from '../components/NoteList'
import ShareList from '../components/ShareList'
import Timeline from '../components/Timeline'
import NoteEditor from '../components/NoteEditor'

export default {
  components: { NoteList, ShareList, Timeline, NoteEditor },
  data () {
    return {
      nickname: localStorage.nickname,
      email: localStorage.email,
      tab: 'note'
    }
  },
  watch: {
    $route () {
      this.routeChanged()
    }
  },
  mounted () {
    this.routeChanged()
  },
  methods: {
    routeChanged () {
      if (this.$route.name) {
        this.tab = this.$route.name
      }
    },
    logoutClick () {
      console.log('logout')
      delete localStorage['token']
      window.location = '/auth.html'
    }
  }
}
</script>

<style lang="less">
html, body {
  height: 100%;
}

#app {
  height: 100%;

  .el-header {
    background-color: #6e0f6d;
    color: whitesmoke;
    display: flex;
    align-items: center;

    .title {
      font-size: 28px;
      font-weight: lighter;
    }

    .nav {
      flex: 1;
      padding-left: 20px;
    }

    .el-dropdown {
      color: whitesmoke;
    }
  }

  .el-aside {
    background-color: #fafafa;
  }

  .el-main {
    padding: 10px;
  }
}
</style>
