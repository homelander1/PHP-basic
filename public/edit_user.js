function confirmDeletion() {
    return confirm("Are you sure you want to delete this user?");
}

document.querySelectorAll('.edit-user-btn').forEach(button => {
    button.addEventListener('click', function () {
        // Get user ID from the button's data attribute
        const userId = this.getAttribute('data-id');

        // Fetch the latest user data from the server
        fetch(`get_user.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const user = data.user;

                    // Populate the modal form with the latest user data
                    document.querySelector('#editUserForm [name="user_id"]').value = user.user_id;
                    document.querySelector('#editUserForm [name="first_name"]').value = user.first_name;
                    document.querySelector('#editUserForm [name="last_name"]').value = user.last_name;
                    document.querySelector('#editUserForm [name="role"]').value = user.role_id;

                    // Display the modal
                    document.getElementById('myModalEditUser').style.display = 'block';
                } else {
                    alert('Failed to fetch user data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the user data.');
            });
    });
});

// Close the modal
function closeModal() {
    document.getElementById('myModalEditUser').style.display = 'none';
}

document.getElementById('editUserForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent page reload

    const formData = new FormData(this); // Get form data

    fetch('update_user.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json()) // Parse  JSON
        .then(data => {
            if (data.status === 'success') {
                closeModal();

                // Update the displayed user details
                const userRow = document.querySelector(`.edit-user-btn[data-id="${data.user.user_id}"]`).closest('.user-item');
                if (userRow) {

                    const nameElement = userRow.querySelector('.user-name strong');
                    nameElement.textContent = `${data.user.first_name} ${data.user.last_name}`;


                    const roleElement = userRow.querySelector('.user-role strong');
                    roleElement.textContent = data.user.role_name;
                }
            } else if (data.status === 'no_changes') {
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the user.');
        });
});
