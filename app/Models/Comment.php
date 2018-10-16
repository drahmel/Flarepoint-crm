<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'description',
        'task_id',
        'user_id'
    ];
    protected $hidden = ['remember_token'];
    protected $orderBy = "id";
    protected $orderDirection = 'desc';


    // Set ORDER BY for all new queries
	public function newQuery($ordered = true)
	{
		$query = parent::newQuery();

		if (empty($ordered)) {
			return $query;
		}

		return $query->orderBy($this->orderBy, $this->orderDirection);
	}

    /**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo('source');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/', $this->description, $matches);

        return $matches[1];
    }

   /* //TODO figure out how to escape the comment, but not the link to the profile, as it just return the full HTML
   public function setDescriptionAttribute($description)
    {
        $this->attributes['description'] = preg_replace(
          '/@([\w\-]+)/',
          'e(<a href="/profiles/$1">$0</a>',
          $description
      );

    }*/
}
