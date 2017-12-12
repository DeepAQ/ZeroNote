$(function () {
  $('#btnLogin').click(() => {
    const email = $('#email').val()
    const password = $('#password').val()
    if (email == '' || password == '') {
      return
    }
    $.ajax({
      type: 'POST',
      url: 'http://localhost:8080/auth/login',
      data: JSON.stringify({
        email: email,
        password: password
      }),
      contentType: 'application/json',
      dataType: 'json',
      timeout: 5000,
      success (data, status, xhr) {
        if (data.success == 1) {
          localStorage.token = data.data.token
          localStorage.nickname = data.data.nickname
          localStorage.email = email
          window.location.reload()
        } else {
          alert(`Login failed: ${data.msg}`)
        }
      },
      error (xhr, errorType, error) {
        alert(`Login failed: ${error}`)
      }
    })
  })
  $('#btnLogout').click(() => {
    delete localStorage['token']
    window.location.reload()
  })

  if (localStorage.token) {
    $('#logged_user').html(`${localStorage.nickname} &lt;${localStorage.email}&gt;`)
    $('#logged').show()
  } else {
    $('#login').show()
  }
})