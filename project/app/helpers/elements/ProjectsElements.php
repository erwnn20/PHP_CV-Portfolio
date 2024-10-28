<?php

class ProjectsElements
{
    public static function displayImg(array|null $images, int $index, $projectID): void
    {
        if ($images) {
            if (count($images) > 1) {
                echo '  <div id="carouselProject-' . $index . '" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">';
                foreach ($images as $image_i => $image)
                    echo '      <button type="button" data-bs-target="#carouselProject-' . $index . '" 
                                    data-bs-slide-to="' . $image_i . '"' . ($image_i == 0 ? ' class="active" aria-current="true"' : '') . '>
                                </button>';
                echo '      </div>
                            <div class="carousel-inner">';
                foreach ($images as $image_i => $image)
                    echo '      <div class="carousel-item ' . ($image_i == 0 ? ' active' : '') . '">
                                    <img src="/public/img/projects/' . $projectID . '/' . $image . '" class="project-image rounded-top w-100" alt="project_image-' . $image_i . '">
                                </div>';
                echo '      </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>';
            } else echo '<img src="/public/img/projects/' . $projectID . '/' . $images[0] . '" class="card-img-top project-image" alt="no image project">';
        } else if ($images !== null)
            echo '<img src="/public/img/projects/no_img.png" class="card-img-top project-image" alt="no image project">';
    }
}