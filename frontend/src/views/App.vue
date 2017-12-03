<template>
  <el-container id="app">
    <el-header height="50px">
      <div class="title">ZeroNote</div>
      <div class="nav"></div>
      <div class="user">
        <el-dropdown>
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
        <el-tabs value="notes">
          <el-tab-pane label="Notes" name="notes">
            <NoteList/>
          </el-tab-pane>
          <el-tab-pane label="Timeline" name="timeline">
            Timeline
          </el-tab-pane>
        </el-tabs>
      </el-aside>
      <el-main>
        <NoteEditor/>
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import NoteList from '../components/NoteList'
import NoteEditor from '../components/NoteEditor'

export default {
  components: { NoteList, NoteEditor },
  data () {
    return {
      nickname: localStorage.nickname,
      email: localStorage.email
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
}
</style>
