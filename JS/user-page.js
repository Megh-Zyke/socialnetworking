function openEditView(postId) {
  const editView = document.querySelector(".edit-post-view");
  editView.style.display = "block";
  // Uncomment if you want to display the cover element as well
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "block";

  currentPostId = postId;
  const hiddenInput = editView.querySelector(".post-id-edit-post");
  hiddenInput.value = postId;
  const previousCaption = editView.querySelector(".previous-caption-data");
  const captionElement = document.getElementById("caption-post-" + postId);

  if (captionElement && captionElement.innerHTML.trim().length > 0) {
    previousCaption.innerHTML = captionElement.innerHTML;
  } else {
    previousCaption.innerHTML = "There is no caption for this post";
  }
}

function openEditBioView() {
  const editView = document.querySelector(".edit-bio-view");
  editView.style.display = "block";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "block";
  const bioElement = document.querySelector("#bio-info");
  const bioText = bioElement.textContent.trim();
  console.log(bioText);
  document.querySelector(".previous-bio-data").innerHTML = bioText;
}

function closeEditView() {
  const editView = document.querySelector(".edit-post-view");
  editView.style.display = "none";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "none";
}

function closeEditBio() {
  const editView = document.querySelector(".edit-bio-view");
  editView.style.display = "none";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "none";
}

function saveBio() {
  var bioElement = document.querySelector(".edit-bio-caption-text");
  var newBio = bioElement.value.trim();

  document.querySelector(".bio").value = newBio;

  const closeBtn = document.querySelector(".close-edit-bio");
  closeBtn.click();

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_bio.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log("Bio updated successfully");
        closeEditBio();
      } else {
        console.error("Error updating bio: " + xhr.responseText);
      }
    }
  };
  xhr.send("bio=" + encodeURIComponent(newBio));
}
