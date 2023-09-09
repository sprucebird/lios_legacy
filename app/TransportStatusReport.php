<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\MediaStream;

class TransportStatusReport extends Model implements HasMedia
{
	protected $fillable = ['VAT', 'comments', 'media', 'created_at'];
	//

	use HasMediaTrait;
	public function registerMediaConversions(Media $media = null)
	{
		$this->addMediaConversion('jpg')
			->width(1080)
			->height(920)->nonQueued();
		$this->addMediaConversion('thumb')
			->width(105)
			->height(45);
	}

	public static function mediaFromId($id)
	{
		return Media::where('model_id', $id)->get();
	}

	public function getThisMedia()
	{
		return Media::where('model_id', $this->id)->where('model_type', '!=', 'App\Deletion')->get();
	}

	public function downloadThisMedia()
	{
		return MediaStream::create('Ataskaita' . $this->created_at)->addMedia($this->getThisMedia());
	}

	public function populate()
	{
		$transport = Transport::all();
		$report = $this;
		foreach ($transport as $tr) {
			if ($report->VAT == $tr->VAT) {
				$report->manufacturer = $tr->manufacturer;
				$report->model = $tr->model;
				$report->rlYear = $tr->rlYear;
				$report->calendarId = 1;
				$report->category = 'task';
				$report->title = $report->VAT;
				$report->dueDateClass = '';
				$report->start = date(DATE_ISO8601, strtotime($report->created_at));
				$report->end = date(DATE_ISO8601, strtotime($report->created_at));
				break;
			}
		}
		$med = $report->getThisMedia();
		if (count($med) <= 0) $report->media = null;
		else {
			foreach ($med as $key => $img) {
				try {
					$sz = getimagesize($img->getPath());
					$report->media[$key] = array(
						'src' => $img->getUrl("jpg"),
						'thumb' => $img->getUrl("thumb"),
						'w' => $sz[0],
						'h' => $sz[1]
					);
				} catch (\Exception $e) {
					$report->media[$key] = null;
				}
			}
			try {
				$report->thumb = $med[0]->getUrl('thumb');
			} catch (\Exception $e) {
				$report->thumb = null;
			}
		}
		return;
	}
}
