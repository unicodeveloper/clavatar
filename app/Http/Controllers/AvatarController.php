<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Effect;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Quality;
use Cloudinary\Transformation\RoundCorners;
use Cloudinary\Transformation\ArtisticFilter;
use Cloudinary\Transformation\Border;
use Cloudinary\Transformation\Argument\Color;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
    }

    public function retrieveAvatar(Request $request, $hash)
    {
        $defaultImageTag = $this->cloudinary->imageTag('default')->serialize();
        $defaultImageUrl = $this->cloudinary->image('default')->toUrl();

        $user = User::where('hash', $hash)->first();

        if(is_null($user)) {
            return $defaultImageTag;
        }

        $publicId = $user->avatar_id;

        $imageUrl = $this->cloudinary->image($publicId);
        $imageTag = $this->cloudinary->imageTag($publicId);

        if($request->has('s') || $request->has('size')) {
            $size = $request->query('s') ?: $request->query('size');

            $imageUrl->resize(Resize::fill($size, $size));
            $imageTag->resize(Resize::fill($size, $size));
        } else {
            $imageUrl->resize(Resize::fill(100,100));
            $imageTag->resize(Resize::fill(100,100));
        }

        if($request->has('d')) {
            $defaultOption = $request->query('d');

            if($defaultOption == 'cp') {
                $imageUrl->effect(Effect::cartoonify());
                $imageTag->effect(Effect::cartoonify());
            }

            if($defaultOption == 'bw') {
                $imageUrl->effect(Effect::blackWhite(30));
                $imageTag->effect(Effect::blackWhite(30));
            }

            if($defaultOption == 'hk') {
                $imageUrl->effect(Effect::artisticFilter(ArtisticFilter::HOKUSAI));
                $imageTag->effect(Effect::artisticFilter(ArtisticFilter::HOKUSAI));
            }
        }

        if($request->has('f')) {
            $formatOption = $request->query('f');

            if($formatOption == 'auto') {
                $imageUrl->format(Format::auto());
                $imageTag->format(Format::auto());
            }
        }

        if($request->has('q')) {
            $qualityOption = $request->query('q');

            if($qualityOption == 'auto') {
                $imageUrl->quality(Quality::auto());
                $imageTag->quality(Quality::auto());
            }
        }

        if($request->has('rc')) {
            $roundedOption = $request->query('rc');

            if($roundedOption == 'y') {
                $imageUrl->roundCorners(RoundCorners::max());
                $imageTag->roundCorners(RoundCorners::max());
            }
        }

        if($request->has('r')) {
            $roundedOption = $request->query('r');

            $imageUrl->rotate($roundedOption);
            $imageTag->rotate($roundedOption);
        }

        if($request->has('b')) {
            $borderOption = $request->query('b');

            $imageUrl->border(Border::solid()->width(3)->color($borderOption));
            $imageTag->border(Border::solid()->width(3)->color($borderOption));
        }

        return $imageTag->serialize();
    }

    public function uploadImage(Request $request)
    {
        $image  = $request->file('image');
        $userId = $request->get('user_id');

        $uploadedImage = $this->cloudinary->uploadApi()->upload($image->getRealPath());

        if($uploadedImage) {

            User::where('id', $userId)->update(['avatar_id' => $uploadedImage['public_id']]);

            return response()->json([
                'status' => true,
                'message' => 'Upload successful'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Upload failed. Please try again'
        ], 500);
    }
}
