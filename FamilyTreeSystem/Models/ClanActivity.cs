using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 宗族活动模型
    /// </summary>
    public class ClanActivity
    {
        /// <summary>
        /// 宗族活动主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 活动标题，必填，最长255个字符
        /// </summary>
        [Required]
        [StringLength(100)]
        [DisplayName("标题")]
        public required string Title { get; set; }

        /// <summary>
        /// 活动描述，必填
        /// </summary>
        [Required]
        [DisplayName("描述")]
        public required string Description { get; set; }

        /// <summary>
        /// 活动地点，必填，最长255个字符
        /// </summary>
        [Required]
        [StringLength(255)]
        [DisplayName("地点")]
        public required string Location { get; set; }

        /// <summary>
        /// 活动类别，必填，最长100个字符
        /// </summary>
        [Required]
        [StringLength(100)]
        [DisplayName("类别")]
        public required string Category { get; set; }

        /// <summary>
        /// 活动举行日期，必填
        /// </summary>
        [Required]
        [DisplayName("活动日期")]
        public DateTime ActivityDate { get; set; }

        /// <summary>
        /// 活动创建日期，可空
        /// </summary>
        [DisplayName("创建日期")]
        public DateTime? CreatedDate { get; set; }

        /// <summary>
        /// 创建该活动的用户ID，必填
        /// </summary>
        [Required]
        [DisplayName("创建人ID")]
        public int UserId { get; set; }

        /// <summary>
        /// 活动系统创建时间，默认当前时间
        /// </summary>
        [DisplayName("系统创建时间")]
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        /// <summary>
        /// 活动系统更新时间，默认当前时间
        /// </summary>
        [DisplayName("系统更新时间")]
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 创建该活动的用户导航属性
        /// </summary>
        [ForeignKey("UserId")]
        public User? User { get; set; }
    }
}