chrome.contextMenus.create({
  id: 'zn_send',
  title: 'Send to ZeroNote',
  contexts: ['selection'],
  onclick (info, tab) {
    if (!localStorage.token) {
      alert('You have not logged in, please log in first')
      return
    }
    const content = info.selectionText
    $.ajax({
      type: 'POST',
      url: 'http://localhost:8080/note/create',
      data: JSON.stringify({
        content: content
      }),
      contentType: 'application/json',
      dataType: 'json',
      headers: {
        'X-Auth-Token': localStorage.token
      },
      timeout: 5000,
      success (data, status, xhr) {
        if (data.success == 1) {
          window.open(`http://localhost:8081/index.html#/note/0/${data.data}`)
        } else {
          alert(`Send failed: ${data.msg}`)
        }
      },
      error (xhr, errorType, error) {
        alert(`Send failed: ${error}`)
      }
    })
  }
});