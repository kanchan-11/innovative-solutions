<?php
$services = get_categories(array(
    'taxonomy'   => 'category', 
    'parent'     => 0, 
    'orderby'    => 'name',
    'hide_empty' => false, 
));
$industries = get_terms(array(
    'taxonomy' => 'post_tag',
    'hide_empty' => false,
));
if ($services || $industries) :
    ?>
    <form method="post" action="settings.php">
        <fieldset>
            <legend><strong>Select Service/Subservice or Industry</strong></legend>
            <div style="margin-bottom: 20px;">
                <strong>Industry:</strong>
                <div style="margin-left: 20px;">
                    <?php foreach ($industries as $industry) : ?>
                        <div>
                            <input type="radio" id="industry-<?php echo $industry->term_id; ?>" name="industry" value="<?php echo $industry->slug; ?>" onclick="updateShortcodeAndFetchPosts('industry', '<?php echo $industry->slug; ?>')">
                            <label for="industry-<?php echo $industry->term_id; ?>"><?php echo $industry->name; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <strong>Services and sub-services:</strong>
                <div style="margin-left: 20px;">
                    <?php foreach ($services as $service) :
                $service_names = get_categories(array(
                    'taxonomy'   => 'category',
                    'parent'     => $service->term_id, 
                    'orderby'    => 'name',
                    'hide_empty' => false,
                ));
                ?>
                <div style="margin-top: 20px;">
                    <?php if ($service_names) : ?>
                        <div style="margin-left: 20px; margin-top: 10px;">
                            <?php foreach ($service_names as $service_name) : ?>
                                <div style="margin-bottom: 10px;">
                                    <input type="radio" id="service-name-<?php echo $service_name->term_id; ?>" name="service_name" value="<?php echo $service_name->slug; ?>" onclick="updateShortcodeAndFetchPosts('service_name', '<?php echo $service_name->slug; ?>')" required>
                                    <label for="service-name-<?php echo $service_name->term_id; ?>"><?php echo $service_name->name; ?></label>
                                    <?php
                                    $subservices = get_categories(array(
                                        'taxonomy'   => 'category',
                                        'parent'     => $service_name->term_id, 
                                        'orderby'    => 'name',
                                        'hide_empty' => false,
                                    ));
                                    if ($subservices) :
                                        echo '<div style="margin-left: 20px;">'; 
                                        foreach ($subservices as $subservice) :
                                            ?>
                                            <div>
                                                <input type="radio" id="subservice-<?php echo $subservice->term_id; ?>" name="subservice" value="<?php echo $subservice->slug; ?>" onclick="updateShortcodeAndFetchPosts('subservice', '<?php echo $subservice->slug; ?>')" required>
                                                <label for="subservice-<?php echo $subservice->term_id; ?>"><?php echo $subservice->name; ?></label>
                                            </div>
                                        <?php endforeach;
                                        echo '</div>';
                                    endif;
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
    </form>
    <div id="post-list" style="margin-top: 20px;">
        <strong>Post Types:</strong>
        <ul id="post-checklist" style="margin-left: 20px;"></ul>
    </div>
    <div style="margin-top: 20px;">
                <strong>Selected Shortcode:</strong> <span id="shortcode-output">[innovative_solutions]</span>
            </div>
            <div style="margin-top: 20px;">
                <button type="button" id="generate-shortcode">Generate Shortcode</button>
            </div>
<?php
else :
    echo '<p>No services found.</p>';
endif;
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".post-checkbox").forEach(function(checkbox) {
        checkbox.addEventListener("change", function() {
            let group = this.getAttribute("data-group");
            let checkedBoxes = document.querySelectorAll('input[data-group="' + group + '"]:checked');
            if (checkedBoxes.length > 3) {
                alert("You can only select up to 3 posts per category.");
                this.checked = false;
            }
        });
    });
    var industryRadios = document.querySelectorAll('input[name="industry"]');
    var serviceRadios = document.querySelectorAll('input[name="service_name"]');
    var subserviceRadios = document.querySelectorAll('input[name="subservice"]');
    industryRadios.forEach(function (industry) {
        industry.addEventListener("change", function () {
            serviceRadios.forEach(service => service.checked = false);
            subserviceRadios.forEach(subservice => subservice.checked = false);
            updateShortcodeAndFetchPosts(this.name,this.value)
        });
    });
    serviceRadios.forEach(function (service) {
        service.addEventListener("change", function () {
            subserviceRadios.forEach(subservice => subservice.checked = false);
            industryRadios.forEach(industry => industry.checked = false);
        });
    });
    subserviceRadios.forEach(function (subservice) {
        subservice.addEventListener("change", function () {
            serviceRadios.forEach(service => service.checked = false);
            industryRadios.forEach(industry => industry.checked = false);
        });
    });
});
function updateShortcodeAndFetchPosts(type, slug) {
    selectedCounts={}
    fetchPosts(type, slug);
}
var selectedCounts = {};
function fetchPosts(type, slug) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var data = 'action=fetch_posts_by_category&type=' + type + '&slug=' + slug;
    xhr.onload = function () {
        if (xhr.status == 200) {
            var posts = JSON.parse(xhr.responseText);
            var postList = document.getElementById('post-checklist');
            postList.innerHTML = '';
            if (posts.length > 0) {
                posts.forEach(function (group) {
                    var categoryHeader = document.createElement('div');
                    categoryHeader.textContent = group.post_type_category;
                    postList.appendChild(categoryHeader);
                    if (!selectedCounts[group.post_type_category]) {
                        selectedCounts[group.post_type_category] = 0;
                    }
                    var postContainer = document.createElement('ul');
                    postContainer.dataset.category = group.post_type_category; 
                    group.posts.forEach(function (post) {
                        var listItem = document.createElement('li');
                        var checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.classList.add('post-checkbox'); 
                        checkbox.dataset.category = group.post_type_category; 
                        checkbox.id = 'post-' + post.id;
                        checkbox.value = post.id;
                        listItem.appendChild(checkbox);
                        listItem.innerHTML += ' ' + post.title;
                        postContainer.appendChild(listItem);
                    });
                    postList.appendChild(postContainer);
                });
            } else {
                postList.innerHTML = '<li>No posts found for the selected category.</li>';
            }
        }
    };
    xhr.send(data);
}
document.getElementById('post-checklist').addEventListener('change', function (event) {
    if (event.target && event.target.classList.contains('post-checkbox')) {
        var checkbox = event.target;
        var category = checkbox.dataset.category;

        if (!selectedCounts[category]) {
            selectedCounts[category] = 0;
        }

        if (checkbox.checked) {
            if (selectedCounts[category] >= 3) {
                checkbox.checked = false; 
                alert(`You can only select up to 3 posts in ${category}`);
            } else {
                selectedCounts[category]++;
            }
        } else {
            selectedCounts[category]--; 
        }

        document.querySelectorAll(`input[data-category="${category}"]`).forEach(cb => {
            if (selectedCounts[category] >= 3 && !cb.checked) {
                cb.disabled = true;
            } else {
                cb.disabled = false;
            }
        });

    }
});
document.getElementById("generate-shortcode").addEventListener("click", function() {
        var shortcode = '[innovative_solutions';
        var selectedIndustry = document.querySelector('input[name="industry"]:checked');
        if (selectedIndustry) {
            shortcode += ' industry="' + selectedIndustry.value + '"';
        }
        var selectedService = document.querySelector('input[name="service_name"]:checked');
        if (selectedService) {
            shortcode += ' service_name="' + selectedService.value + '"';
        }
        var selectedSubservice = document.querySelector('input[name="subservice"]:checked');
        if (selectedSubservice) {
            shortcode += ' subservice="' + selectedSubservice.value + '"';
        }
var selectedPostsByType = {};
document.querySelectorAll('.post-checkbox:checked').forEach(function(checkbox) {
    var postTypeCategory = checkbox.dataset.category;
    var postId = checkbox.value;
    if (!selectedPostsByType[postTypeCategory]) {
        selectedPostsByType[postTypeCategory] = [];
    }
    selectedPostsByType[postTypeCategory].push(postId);
});
Object.keys(selectedPostsByType).forEach(function(postType) {
    var postIds = selectedPostsByType[postType];
    if (postIds.length > 0) {
        shortcode += ' ' + postType + '="' + postIds.join(',') + '"';
    }
});
        shortcode += ']';
        document.getElementById('shortcode-output').textContent = shortcode;
    });

</script>
