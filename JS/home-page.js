let currentPostId = null;

function postComment(postId) {
  var commentTextField = document.querySelector(
    "#comment-text-field-" + postId
  );
  var commentText = commentTextField.value.trim();

  console.log("Clicked");
  console.log(commentText);
  if (commentText === "") {
    alert("Please enter a comment");
    return;
  }

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "add_comment.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        // Comment added successfully
        console.log("success");
      } else {
        alert("Error adding comment: " + xhr.responseText);
      }
    }
  };
  xhr.send(
    "post_id=" + postId + "&comment_text=" + encodeURIComponent(commentText)
  );

  const viewCommentedDiv = document.getElementById("commented" + postId);
  viewCommentedDiv.style.display = "block";
  commentTextField.value = ""; // Clear the text field
}

function autoGrow(element) {
  element.style.height = "auto";
  element.style.height = element.scrollHeight + "px";
}
const textarea = document.querySelector("textarea");
autoGrow(textarea);

textarea.addEventListener("focus", function (event) {
  event.target.blur();
});

function openComments(postID) {
  const viewAllComments = document.getElementById(postID);
  viewAllComments.style.display = "block";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "block";
}

function openShareDiv(postID) {
  currentPostId = postID;
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "block";
  const shareOptions = document.querySelector(".share-options-avaliable");
  shareOptions.style.display = "block";
}

function closeComments(postID) {
  const viewAllComments = document.getElementById(postID);
  viewAllComments.style.display = "none";
  const coverElement = document.querySelector(".cover-element");
  coverElement.style.display = "none";
}

const closeButton = document.querySelector(".close-button");
if (closeButton) {
  closeButton.addEventListener("click", function () {
    const coverElement = document.querySelector(".cover-element");
    coverElement.style.display = "none";
    const shareOptions = document.querySelector(".share-options-avaliable");
    shareOptions.style.display = "none";
  });
}

const shareText = document.getElementById("share-text");

var text = shareText.innerHTML;

const whatsapp = document.querySelector(".fa-whatsapp");
if (whatsapp) {
  whatsapp.addEventListener("mouseenter", function () {
    shareText.innerHTML = text + "\n Share on whatsapp?";
  });

  whatsapp.addEventListener("mouseleave", function () {
    shareText.innerHTML = text;
  });
}

const x = document.querySelector(".fa-x-twitter");
if (x) {
  x.addEventListener("mouseenter", function () {
    shareText.innerHTML = text + "\n Share on X?";
  });

  x.addEventListener("mouseleave", function () {
    shareText.innerHTML = text;
  });
}

const copy = document.querySelector(".fa-copy");
if (copy) {
  copy.addEventListener("mouseenter", function () {
    shareText.innerHTML = text + "\n Copy Link?";
  });

  copy.addEventListener("mouseleave", function () {
    shareText.innerHTML = text;
  });
}

document.getElementById("copyLinkLogo").addEventListener("click", function () {
  var link =
    window.location.origin +
    "/post-details.php?post_id=?njz" +
    currentPostId +
    "?Lkkj";
  navigator.clipboard.writeText(link).then(
    function () {
      alert("Link copied to clipboard");
    },
    function (err) {
      console.error("Failed to copy link: ", err);
    }
  );
  hideShareMenu();
});

document.getElementById("whatsappLogo").addEventListener("click", function () {
  var link =
    window.location.origin +
    "/post-details.php?post_id=Sn?uiW" +
    currentPostId +
    "#Kibl";
  var message =
    "Hey! Check out this interesting post I found: \n Here's the link: " +
    encodeURIComponent(link) +
    " \n\nLet me know what you think!";
  var whatsappUrl = "https://wa.me/?text= " + message;
  window.open(whatsappUrl, "_blank");
  hideShareMenu();
});

document.getElementById("twitterButton").addEventListener("click", function () {
  // URL of the post you want to share
  var postUrl =
    window.location.origin +
    "/post-details.php?post_id=Sn?uiW" +
    currentPostId +
    "#Kibl";

  // Tweet text
  var tweetText = "Check out this post: " + postUrl;

  // Construct Twitter share URL
  var twitterUrl =
    "https://twitter.com/intent/tweet?text=" + encodeURIComponent(tweetText);

  // Open a new window with the Twitter compose tweet page
  window.open(twitterUrl, "_blank");
});

function addLike(postId) {
  console.log("Adding like to post with ID: " + postId);
  var xhr = new XMLHttpRequest();

  xhr.open("POST", "add_like.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        // Parse the response to get the updated like count
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
          console.log("suceess");
        } else {
          console.error("Failed to add like: " + response.message);
        }
      } else {
        console.error("Failed to add like: " + xhr.status);
      }
    }
  };

  var data = JSON.stringify({ post_id: postId });
  xhr.send(data);
}

function changeColor(postId) {
  var likeBtn = document.getElementById("like" + postId);
  var currentColor = likeBtn.style.color;

  console.log("Current color: " + currentColor);
  console.log("Changing color");

  // Change the color based on the current color
  if (currentColor === "rgb(255, 253, 0)") {
    // #fffd00 in RGB format
    likeBtn.style.color = "#f1f0f0";
  } else {
    likeBtn.style.color = "#fffd00";
  }
}

function addFriend(user_Id) {
  console.log("Adding Friend Id " + user_Id);
  var xhr = new XMLHttpRequest();

  var acceptButton = document.getElementById(user_Id);
  console.log(acceptButton);

  acceptButton.classList.remove("fa-plus");
  acceptButton.classList.add("fa-paper-plane");
  xhr.open("POST", "send_requests.php", true);

  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log(xhr.responseText);
      } else {
        console.error("Failed to add friend : " + xhr.status);
      }
    }
  };

  var data = JSON.stringify({ user_id: user_Id });
  console.log(data);
  xhr.send(data);
}

function viewOtherUsers(user_id) {
  const url = `other-users-page.php?user-id=${user_id}`;
  window.location.href = url;
}

function previewMedia(event, type) {
  const previewContainer = document.getElementById("previewContainer");
  previewContainer.innerHTML = ""; // Clear previous previews

  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      if (type === "photo") {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.className = "media-preview";
        previewContainer.appendChild(img);
      } else if (type === "video") {
        const video = document.createElement("video");
        video.src = e.target.result;
        video.controls = true;
        video.className = "media-preview";
        previewContainer.appendChild(video);
      }
    };

    reader.readAsDataURL(file);
  }
}

function navigateToPostDetails(postId) {
  const url = `post-details.php?post_id=cE?${postId}fF?`;
  window.location.href = url;
}

function viewProfile() {
  const url = `user-page.php`;
  window.location.href = url;
}

function confirmDelete() {
  var result = confirm("Are you sure you want to delete this post?");
  return result;
}
