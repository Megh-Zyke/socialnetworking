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

function closeEditView() {
  const editView = document.querySelector(".edit-post-view");
  editView.style.display = "none";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "none";
}
