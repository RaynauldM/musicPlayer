let songSelector = document.getElementById("songSelector");

document.addEventListener("DOMContentLoaded", function () {
  var artists = document.querySelectorAll(".artist");
  artists.forEach(function (artist) {
    artist.addEventListener("click", function () {
      var artistId = this.getAttribute("data-artist-id");
      var recordId = this.getAttribute("data-record-id"); // Assuming you have record ID in the artist element
      showRecords(artistId, recordId);
      showSongs(artistId, recordId);
    });
  });

  document
    .getElementById("albumSelector")
    .addEventListener("click", (event) => {
      if (event.target.classList.contains("record")) {
        let recordList = document.getElementsByClassName("record");
        for (el of recordList) {
          el.addEventListener("click", onClick);
        }
      }
    });
});

function onClick(event) {
  let { id, className } = event.target;
  switch (id) {
    case "backBtn":
      location.reload();
      break;
  }
  switch (className) {
    case "record":
      var artistId = this.getAttribute("data-artist-id");
      var recordId = this.getAttribute("data-record-id");
      showSongs(artistId, recordId);
  }
}

function createBackBtn() {
  let btn = document.createElement("button");
  btn.id = "backBtn";
  btn.className = "btn invisible";
  btn.innerText = "Back";
  btn.addEventListener("click", onClick);
  return btn;
}

function showRecords(artistId, recordId) {
  var url = "getRecords.php?artistId=" + artistId;
  if (recordId) {
    url += "&recordId=" + recordId;
  }

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("albumSelector").innerHTML = data;
      document.getElementById("albumSelector").append(createBackBtn());

      // If a record is clicked, show songs
      if (recordId) {
        showSongs(artistId, recordId);
      }
    })
    .catch((error) => console.error("Error:", error));
}

function showSongs(artistId, recordId) {
  console.log("event fired");
  var url = "getSongs.php?artistId=" + artistId;
  if (recordId) {
    url += "&recordId=" + recordId;
  }

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("songSelector").innerHTML = data;
    })
    .catch((error) => console.error("Error:", error));
}
