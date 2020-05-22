function openModal(videolink) {
  document.getElementById("videoiframe").src = videolink;
  document.getElementById("modal-video").style.display = "block";
  document.getElementById("modal-contents").style.display = "block";
  document.body.style.position = 'fixed';
//  document.body.style.width = '100%';
  
  //document.body.style.top = `-${window.scrollY}px`;
  }

function closeModal() {
  document.getElementById("videoiframe").src = "";
  document.getElementById("modal-video").style.display = "none";
  document.getElementById("modal-contents").style.display = "none";
  document.body.style.position = '';
//  document.body.style.top = '';
}

