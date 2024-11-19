

// top section - scroll down button
window.onload = function () {
    const scrollButton = document.getElementById("scrollButton");
    if (scrollButton) {
        scrollButton.addEventListener("click", function () {
            window.scrollBy({
                top: 1300,
                behavior: 'smooth'
            });
        });
    }
};

// Open Follow Us modal
const openFollowUsModal = document.getElementById("openFollowUsModal");
if (openFollowUsModal) {
    openFollowUsModal.onclick = function () {
        document.getElementById("myModal").style.display = "block";
    };
}

// Close Follow Us modal on 'X'
const closeFollowUsModal = document.querySelectorAll(".modal .close")[0];
if (closeFollowUsModal) {
    closeFollowUsModal.onclick = function () {
        document.getElementById("myModal").style.display = "none";
    };
}

// Open Log In modal
const openLoginModal = document.getElementById("openLoginModal");
if (openLoginModal) {
    openLoginModal.onclick = function () {
        document.getElementById("myModalLogin").style.display = "block";
    };
}

// Switch to Sign Up modal from Log In
const switchToSignUp = document.getElementById("switchToSignUp");
if (switchToSignUp) {
    switchToSignUp.onclick = function (event) {
        event.preventDefault();
        document.getElementById("myModalLogin").style.display = "none";
        document.getElementById("myModalSignup").style.display = "block";
    };
}

// Switch to Log In modal from Sign Up
const switchToLogin = document.getElementById("switchToLogin");
if (switchToLogin) {
    switchToLogin.onclick = function (event) {
        event.preventDefault();
        document.getElementById("myModalSignup").style.display = "none";
        document.getElementById("myModalLogin").style.display = "block";
    };
}

// Close both modals when clicking on 'X'
document.querySelectorAll(".modal .close").forEach(closeBtn => {
    closeBtn.onclick = function () {
        const modal = closeBtn.closest(".modal");
        modal.style.display = "none";
    };
});

// Close modal when clicking outside
window.onclick = function (event) {
    const modals = document.querySelectorAll(".modal");
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
};

// Open New Article modal
const openArticleModal = document.getElementById("openArticleModal");
if (openArticleModal) {
    openArticleModal.onclick = function () {
        document.getElementById("myModalArticle").style.display = "block";
    };
}

// Close New Article modal on 'X'
const closeArticleModal = document.querySelectorAll("#myModalArticle .close")[0];
if (closeArticleModal) {
    closeArticleModal.onclick = function () {
        document.getElementById("myModalArticle").style.display = "none";
    };
}




const imageInput = document.getElementById('imageUpload');
const imagePreview = document.getElementById('imagePreview');
const clearImageBtn = document.getElementById('clearImageBtn');

// Image check
if (imageInput) {
    imageInput.addEventListener('change', function () {
        const file = imageInput.files[0];

        // If file is selected and is an image
        if (file && file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                clearImageBtn.style.display = 'inline-block';
                const base64Image = e.target.result;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'image_base64';
                hiddenInput.value = base64Image;
                document.querySelector('form').appendChild(hiddenInput);
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            clearImageBtn.style.display = 'none';
        }
    });
}

// Clear image and reset form
if (clearImageBtn) {
    clearImageBtn.addEventListener('click', function () {
        // Reset the file input and image preview
        imageInput.value = '';
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        clearImageBtn.style.display = 'none';
    });
}


// Open Edit Article modal
document.querySelectorAll('.edit-article-btn').forEach(button => {
    button.addEventListener('click', function () {

        const articleId = this.getAttribute('data-id');
        const articleTitle = this.getAttribute('data-title');
        const articleContent = this.getAttribute('data-content');
        const articleImage = this.getAttribute('data-image');

        document.getElementById('editArticleId').value = articleId;
        document.getElementById('editArticleTitle').value = articleTitle;
        document.getElementById('editArticleContent').value = articleContent;

        const imagePreview = document.getElementById('editImagePreview');
        if (articleImage) {
            imagePreview.src = articleImage;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }

        document.getElementById('myeditArticleModal').style.display = 'block';
    });
});




// Update the thumbnail
document.getElementById('editImageUpload').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const imagePreview = document.getElementById('editImagePreview');

    if (file) {
        const reader = new FileReader();


        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    } else {
        imagePreview.src = '';
        imagePreview.style.display = 'none';
    }
});





// Close Edit Article modal
const closeEditModal = document.getElementById('close-modal-btn');
if (closeEditModal) {
    closeEditModal.addEventListener('click', function () {
        document.getElementById('myeditArticleModal').style.display = 'none';
        const imagePreview = document.getElementById('editImagePreview');
        const editImageUpload = document.getElementById('editImageUpload');

        // Reset the image
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        editImageUpload.value = '';
        document.getElementById('editArticleForm').reset();
        document.getElementById('myeditArticleModal').style.display = 'none';
    });
}

// Cancel Edit Modal Button
const cancelEditBtn = document.getElementById("cancel-btn");
if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', function () {
        document.getElementById('myeditArticleModal').style.display = 'none';
    });
}


document.querySelectorAll('.delete-article-btn').forEach(button => {
    button.addEventListener('click', function () {
        const articleId = this.getAttribute('data-id');
        const confirmed = confirm('Are you sure you want to delete this article?');
        if (confirmed) {

            fetch('delete_article.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ article_id: articleId }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred: ' + error.message);
                });

        }
    });
});





